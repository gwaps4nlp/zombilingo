Nouvelle "anomalie" signalée.

Mode de jeu : {{ $report->mode }}<br/>

{!! str_replace('\r\n','<br />',nl2br($report->message)) !!}
<br/>
<br/>
Lien vers la séquence de jeu :<br/>
{!! link_to('game/admin-game/begin'.'/'.$report->relation_id.'?annotation_id='.$report->annotation_id,null,['target'=>'blank']) !!}