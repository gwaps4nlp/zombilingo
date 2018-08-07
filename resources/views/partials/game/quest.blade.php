<?php $progress = (100*$questuser->getQuestScore($user))/$questuser->getRequiredValue($user);
  $description = $questuser->getQuestDescription($user);
  $key = $questuser->returnKey($user);
?>
<div class="description">
    <br/>
    {{$description}}  
    {{$key}}
    <br/>   
</div>
<div class="progress">   
    <div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
        {{ $progress}}%
    </div>
    <div class="progress-bar progress-bar-danger" id="progress-bars" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
    @if(!$progress)
        0%
    @endif
  </div>
</div>

<style type="text/css">
  #progress-bars{
    border-color: black;
    border: solid;
  }

  .description{
    color: black;
  }
</style>
