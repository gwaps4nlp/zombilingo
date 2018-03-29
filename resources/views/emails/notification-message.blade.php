@extends('emails.template')

@section('content')

<p style="color:#aaaaaa;font-size:15px;font-weight:normal;line-height:24px;margin:0;text-align:center;">
    {{ $username_message }} a commenté sur une discussion à laquelle tu t'intéresses.
</p>
<hr/>
<p style="color:#505050;font-size:16px;font-weight:normal;line-height:24px;margin:0">
    {{ nl2br($username_message) }} a écrit :<br/>
    <span style="color:#888;">{!! nl2br(htmlentities($content)) !!}</span>
</p> 
<p style="color:#505050;font-size:16px;font-weight:normal;line-height:24px;margin:0;margin-top:20px;text-align:center;">
@if(preg_match('/QuestionAnswer/',$entity_type))
    <a style="color:#ffffff;font-weight:700;text-decoration:none;font-size:15px!important;padding:10px 20px;background-color:#0e7f3c;border-radius:18px;" href="{{ Config::get('app.url') }}/faq#discussion_{{ $entity_id }}?show=1&id={{ $message_id }}" target="_blank">Voir la discussion</a>
@else
    <a style="color:#ffffff;font-weight:700;text-decoration:none;font-size:15px!important;padding:10px 20px;background-color:#0e7f3c;border-radius:18px;" href="{{ Config::get('app.url') }}/discussion/index/{{ $discussion_id }}?show=1&id={{ $message_id }}" target="_blank">Voir la discussion</a>
@endif
    <br/><br/>
    <a href="{{ Config::get('app.url') }}/discussion/un-follow-thread?discussion_id={{ $discussion_id }}" style="color:#aaaaaa;font-size:15px;margin-top:30px;">Quitter la discussion</a>
</p>

@stop
