<?php
// example of how to modify HTML contents
include('../simple_html_dom.php');

// get DOM from URL or file
$html = file_get_html('https://secure.e2rm.com/registrant/api/scoreboard.aspx?eventid=23511&scoreboardtype=ind');

foreach($html->find('table[class=list]') as $e)
{
    //$e->outertext = '[INPUT]';
	echo $e;
}
?>