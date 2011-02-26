<?php
include("pulse.config.php");
class Pulse {
	private $style;
	private $votes_table;
	private $format = "%7Bup%7D+upvotes%2C+%7Bdown%7D+downvotes"; // encoded value of '{up} upvotes, {down} downvotes'
	
	function __construct($style=''){
		$this->style = empty($style) ? 'thumb1' : $style;
		$this->votes_table = 'pulse_votes';
	}
	
	function setFormat($tpl) {
		$this->format = urlencode($tpl);
	}
	
	/**
	echo Pulse::css()
	outputs the required css
	@return str
	@scope public
	**/
	public static function css(){
		return "<link rel='stylesheet' href='".PULSE_DIR."/assets/css/pulse.css'></link>";
	}

	/**
	echo Pulse::javascript()
	outputs the required javascript
	@return str
	@scope public
	**/
	public static function javascript(){
		return "<script type=\"text/javascript\" src='".PULSE_DIR."/assets/js/jquery-1.4.2.min.js'></script>\n<script type=\"text/javascript\" src='".PULSE_DIR."/assets/js/pulse.core.js'></script>";
	}
	
	/**
	Checks whether a user has already voted
	@return bool; true if voted before, false if not
	@param item_id int
	@scope public
	**/
	public function votedBefore($item_id){
		if($_COOKIE['pulse_item_'.$item_id] == 1) { // check sessions first; voted before
			return true;
		} else { // session says user hasn't voted yet. So check against IP
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = "SELECT * FROM {$this->votes_table} WHERE `ip` = '$ip' AND `item_id` = $item_id";
			$result = mysql_query($query);
			if(mysql_num_rows($result)>0){ // already voted
				return true;
			} elseif(mysql_num_rows($result)==0){ // haven't voted
				return false;
			}
		}
	}

	/**
	Counts the number of upvotes of a given item
	@param item_id int
	@return int
	@scope public
	**/
	public function countUpVotes($item_id) {
		$query = "SELECT * FROM {$this->votes_table} WHERE `item_id`= $item_id AND `vote_value`>0";
		$result = mysql_query($query);
		$votes = 0;
		while($row = mysql_fetch_assoc($result)){
			$votes+=$row['vote_value'];
		}
		return (int) $votes;
	}

	/**
	Counts the number of down votes of a given item
	@param item_id int
	@return POSITIVE int
	@scope public
	**/
	public function countDownVotes($item_id) {
		$query = "SELECT * FROM {$this->votes_table} WHERE `item_id`= $item_id AND `vote_value`<0";
		$result = mysql_query($query);
		$votes = 0;
		while($row = mysql_fetch_assoc($result)){
			$votes+=$row['vote_value'];
		}
		return (int) -$votes; // returns a POSITIVE integer
	}
	
	/**
	Creates the buttons for voting of a given item
	@param item_id
	@return str html of the buttons
	**/
	private function createButtons($item_id){
		if($this->votedBefore($item_id)==true){
			return false;
		} else {
		$html = <<<EOD
<span class='pulse_vote_buttons $this->style'>
\t\t<a href='javascript:;' class='pulse_vote_button vote_up' data-dir='up' data-itemId='$item_id' data-format='$this->format'>Vote Up!</a>
\t\t<a href='javascript:;' class='pulse_vote_button vote_down' data-dir='down' data-itemId='$item_id' data-format='$this->format'>Vote Down!</a> 
\t</span>
EOD;
		return $html;
		}
	}
	
	/**
	Format the result of the voting 
	@param  item_id int
			format str
				{up} is replaced by number of upvotes
				{down} is replaced by number of downvotes
				{balance} is replaced by difference between up and down votes
	@return str formatted result
	@scope public
	**/
	public function getFormatted($item_id, $format) { // get formatted results
		$upVotes = $this->countUpVotes($item_id);
		$downVotes = $this->countDownVotes($item_id);
		$balance = $upVotes - $downVotes;
		$result = preg_replace('/{up}/',$upVotes, urldecode($format));
		$result = preg_replace('/{down}/', $downVotes, $result);
		$result = preg_replace('/{balance}/',$balance, $result);
		return $result;
	}
	
	/**
	Creates everything needed for voting including buttons and formatted result
	@param item_id int
	@return str HTML for the voting buttons and result
	@scope public
	**/
	public function voteHTML($item_id) {
		$html = "<span class='pulse_votes_container'>\n".$this->createButtons($item_id)."\n<span class='pulse_result_format'>".$this->getFormatted($item_id, $this->format)."</span>\n</span>";
		return $html;
	}
	
	/**
	Votes up on a given item
	@param item_id int
	@return bool
			true if vote is successful
			false if voting fails
	@scope public
	**/
	public function voteUp($item_id){
		if(!$this->votedBefore($item_id)){ // check whether already voted.
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = "INSERT INTO {$this->votes_table} (`item_id`, `vote_value`, `ip`) VALUES ($item_id, 1, '$ip')";
			$result = mysql_query($query);
			if(mysql_affected_rows()==1){ // vote done
				return true;
			} else { // cannot vote; probably mysql error
				return false;
			}
		}
	}

	/**
	Votes down on a given item
	@param item_id int
	@return bool
			true if vote is successful
			false if voting fails
	@scope public
	**/
	public function voteDown($item_id){
		if(!$this->votedBefore($item_id)){ // check whether already voted.
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = "INSERT INTO {$this->votes_table} (`item_id`, `vote_value`, `ip`) VALUES ($item_id, -1, '$ip')";
			$result = mysql_query($query);
			if(mysql_affected_rows()==1){ // vote done
				return true;
			} else { // cannot vote; probably mysql error
				return false;
			}
		}
	}
}
?>