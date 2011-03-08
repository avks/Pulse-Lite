CREATE TABLE `pulse_votes` (
   `id` int(11) not null auto_increment,
   `item_id` int(11),
   `vote_value` int(11),
   `ip` varchar(255),
   `date` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) 