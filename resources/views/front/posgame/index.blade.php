@extends('front.template')

@section('main')
<?php
$ids = [];
?>
<!-- <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<div class="row">
    <div class="col-md-10 col-md-offset-1 center" id="block-game"> -->

        <div class="row">

            <div class="col-md-10">
                <h1>Choisissez la bonne catégorie</h1>
                <h1 id="nom_versus" style="font-size : 55px">{{ trans('pos.'.$pos_game->pos1->slug) }} vs.{{ trans('pos.'.$pos_game->pos2->slug) }}&#8239<sup>
                        <i class="fa fa-question-circle fa-6" style="color: #fccc9b;cursor:pointer;font-size:1em;" data-toggle="collapse" data-target="#demo"></i></sup> </h1>
                <h3  style="color:black;font-size: 0.9em; cursor: pointer;  text-align: center; margin-bottom:10px"></h3>
                <div style="text-align: center; padding-bottom:15px">  
                    <a onclick="reload()" style="cursor: pointer" >Générer un autre versus</a>
                </div>
                <div id="demo" style="padding: 15px" class="collapse">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </div>            
            </div>
            <div class="col-md-2" style="cursor : pointer">
                <h4 class="help-trigger" style="position:relative; padding-left:63%; width:0;">{{ trans('game.need-help') }}</h4>
                <div class="savant help-trigger"></div>
            </div>
        </div>
        <div class="row" style="height : 100%">
            <!--<div class="wrapper">-->
            <!--            <div class="content">-->

            <div id="main">
                {!! Form::open(['url' => 'pos-game/index', 'method' => 'post', 'role' => 'form', 'id' => 'form-pos']) !!}

                <table style="">
                    <tr>
                        <td style=" width:60%">
                        </td>
                        <td id="stats-label" align="center" style="text-align: center; width: 100%; display: none"><h1> Statistiques des réponses </h1></td>
                    </tr>
                    @foreach($annotations as $annotation)
                    <?php
                    $ids[] = $annotation->id;
                    ?>

                    <tr id="line_{{ $annotation->id }}">
                        <td style=" width:60%">
                            <div id="sentence[{{ $annotation->id }}]" class="sentence" data-focus="{{ $annotation->word_position}}">{{ trim($annotation->sentence->content) }}</div>

                        </td>

                        <!--CHART-->
                        <td align="center" class="cell_chart" id="chart_{{ $annotation->id }}" style="text-align: center; padding-left: 15px; width:100%; padding-top:15px; right:15px; display:block">

                        </td>

                        <!--ANNOTATION CELL-->
                        <td class="cell" id="cell_{{ $annotation->id }}">
                            <ul class="nav nav-tabs" name="tab_{{ $annotation->id }}">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#vs_{{ $annotation->id }}" data-toggle="tab" role="tab">Versus</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab_other_{{ $annotation->id }}" data-toggle="tab" role="tab">Ni l'un ni l'autre !</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="vs_{{ $annotation->id }}">
                                    <!-- pretty shrinking -->
                                    <div style="overflow: hidden; white-space: nowrap">
                                        <a  class="leftlabel full" name="leftlabel[{{ $annotation->id }}]" style="margin-right: 0.8em;"> <br> {{ $pos_game->pos1->slug }}
                                            <br>({{ trans('pos.'.$pos_game->pos1->slug) }})</a>

                                        <a class="leftlabel shrinked" name="leftlabel_shrinked[{{ $annotation->id }}]" style="margin-right: 0.8em; display:  none"> <br> {{ $pos_game->pos1->slug }}</a>
                                        <input style="width : 70%"
                                               min="1"
                                               max="5"
                                               value="1"
                                               step="1"
                                               list="range-list"
                                               type="range"
                                               class="slide show-labeltooltip show-activevaluetooltip hide-ticks"
                                               name="answer[{{ $annotation->id }}]"
                                               id="answer[{{ $annotation->id }}]"
                                               data-range='{
                                               "calcTrail": false,
                                               "calculateWidth": false
                                               }'
                                               />
                                        <datalist id="range-list" style="width:20%">;
                                            <select>
                                                <option id="range1" value="{{ $annotation->pos1 }}_2"></option>
                                                <option id="range2" value="{{ $annotation->pos1 }}_1"></option>
                                                <option id="range3" value="{{ $annotation->pos2 }}_1"></option>
                                                <option id="range4" value="{{ $annotation->pos2 }}_2"></option>
                                            </select>
                                        </datalist>
                                        <a class="rightlabel full" name="rightlabel[{{ $annotation->id }}]" >{{ $pos_game->pos2->slug }}
                                            <br> ({{ trans('pos.'.$pos_game->pos2->slug) }})</a>
                                        <a  class="rightlabel shrinked" name="rightlabel_shrinked[{{ $annotation->id }}]" style="display:  none"> <br> {{ $pos_game->pos2->slug }}</a>
                                    </div>
                                    <!--</div>-->
                                    <!--                            <label><input type="radio" value="{{ $annotation->pos1 }}_2" name="answer[{{ $annotation->id }}]" /> {{ trans('pos.'.$annotation->pos1) }}</label><br/>
                                                                    <label><input type="radio" value="{{ $annotation->pos1 }}_1" name="answer[{{ $annotation->id }}]" /> Plutôt {{ trans('pos.'.$annotation->pos1) }}</label><br/>
                                                                    <label><input type="radio" value="0" name="answer[{{ $annotation->id }}]" />
                                                                    <select name="other_{{ $annotation->id }}" id="other_{{ $annotation->id }}" class="select_pos">
                                                                        <option value="0">Sélectionner....</option>
                                                                        <option value="UNK_0">Je ne sais pas</option>
                                                                        @foreach($pos_game->cat_pos as $cat_pos)
                                                                        <option value="{{ $cat_pos->slug }}_2">{{ trans('pos.'.$cat_pos->slug) }}</option>
                                                                        @endforeach
                                                                        <option value="UNK_2">Autre</option>
                                                                    </select><br/>
                                                                    <label><input type="radio" value="{{ $annotation->pos2 }}_1" name="answer[{{ $annotation->id }}]" /> Plutôt {{ trans('pos.'.$annotation->pos2) }}</label><br/>
                                                                    <label><input type="radio" value="{{ $annotation->pos2 }}_2" name="answer[{{ $annotation->id }}]" /> {{ trans('pos.'.$annotation->pos2) }}</label><br/>                               -->

                                <!--<label><input type="radio" style="display : none" value="0" name="answer[{{ $annotation->id }}]" />-->
                                    <br/>
                                </div>
                                <div class="tab-pane tab-other" id="tab_other_{{ $annotation->id }}">
                                    <div class="text-center" style="margin-top:15px">
                                        <!--                                        <button type="button" class="btn btn_unk btn-primary" name="unk_{{ $annotation->id }}" id="unk_{{ $annotation->id }}" 
                                                                                        value="UNK_0">
                                                                                    Je ne sais pas</button>-->
<!--                                        <label><input type="radio" value="3" name="unk_{{ $annotation->id }} " id="unk_{{ $annotation->id }}" />
                                            Je ne sais pas </label><br/>-->

<!--                                        <input type="button" form="form-pos" class="btn btn_unk btn-primary" name="unk_{{ $annotation->id }}" id="unk_{{ $annotation->id }}" 
value="UNK_0"/>-->
                                        <select  class="select_pos" name="other_{{ $annotation->id }}" style="position: relative; display:inline-block; margin-left: 15px" id="other_{{ $annotation->id }}">
                                            <option value="0">Autre....</option>
                                            <!--<option value="UNK_0">Je ne sais pas</option>-->
                                            @foreach($pos_game->cat_pos as $cat_pos)
                                            <option value="{{ $cat_pos->slug }}_2">{{ trans('pos.'.$cat_pos->slug) }}</option>
                                            @endforeach
                                            <option value="UNK_2">Autre</option>
                                        </select><br/>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td valign="bottom" id="checked_{{ $annotation->id }}" style="text-align:center; visibility:hidden">
                            <i class="fa fa-check-circle" style="color: lightgreen;font-size:1.5em;"></i>
                            <div id="checked_txt_{{ $annotation->id }}"></div>
                        </td>
                        </tbody>
                    </tr>

                    @endforeach

                </table>

            </div>

            <div id="validation-button" class="form-group col-lg-3 col-md-offset-6">
                <input type="submit" value="Valider" class="btn btn-success main-button" />
            </div>
            {!! Form::close() !!}

            <div id="next-button" class="form-group col-lg-3 col-md-offset-6" style="display:none">
                <button class="main-button btn btn-success" onclick="reload()" ><b> Générer un autre versus </b></button>
            </div>  
            <!-- Help Accordion Menu -->
            <div id="help" style="margin-top: 15px" >
                @foreach($catPosPosGames as $catPosPosGame)
                <button class="accordion" > {{ $catPosPosGame->slug }} ({{ trans('pos.'.$catPosPosGame->slug) }})</button>
                <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod 
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>
                @endforeach
                <!--</div>-->
            </div>
        </div>
        <div id="chart_1" style="padding-left: 15px; padding-top:15px; right:15px; margin-right: 50px; display:block">

        </div>
        <div id="response-div" style="display : none"></div>
<!-- 


    </div>
</div> -->
@stop

@section('css')
<style type="text/css">
    .cell {
        vertical-align: top;
        text-align: left;
        padding-left: 0px;
        width:35%;
        padding-top:15px;
        word-wrap:break-word;        
    }
    .tab-other {
        min-height: 96px;
    }
    .main-button {
        font-size: 1.2em;
        /*        width: 155px;*/
        height: 65px;
        border: none;
        margin-top: 55px;
        margin-left: 50%;
        padding: 15px;
    }
    fieldset > * {
        vertical-align: middle;
    }
    /* Special styling for WebKit/Blink */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        border: 1px solid #000000;
        height: 36px;
        width: 16px;
        border-radius: 3px;
        background: #ffffff;
        cursor: pointer;
        margin-top: -14px; /* You need to specify a margin in Chrome, but in Firefox and IE it is automatic */
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d; /* Add cool effects to your sliders! */
    }

    /* All the same stuff for Firefox */
    input[type=range]::-moz-range-thumb {
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
        border: 1px solid #000000;
        height: 36px;
        width: 16px;
        border-radius: 3px;
        background: #ffffff;
        cursor: pointer;
    }

    /* All the same stuff for IE */
    input[type=range]::-ms-thumb {
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
        border: 1px solid #000000;
        height: 36px;
        width: 16px;
        border-radius: 3px;
        background: #ffffff;
        cursor: pointer;
    }
    input[type=range] {
        -webkit-appearance: none; /* Hides the slider so that custom slider can be made */
        width: 80%; /* Specific width is required for Firefox. */
        background: transparent; /* Otherwise white in Chrome */
    }

    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
    }

    input[type=range]:focus {
        outline: none; /* Removes the blue border. You should probably do some kind of focus styling for accessibility reasons though. */
    }

    input[type=range]::-ms-track {
        width: 100%;
        cursor: pointer;

        /* Hides the slider so custom styles can be added */
        background: pink; 
        border-color: black;
        color: black;
    }

    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 8.4px;
        cursor: pointer;
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
        background: #3071a9;
        border-radius: 1.3px;
        border: 0.2px solid #010101;
    }

    input[type=range]:focus::-webkit-slider-runnable-track {
        background: #367ebd;
    }

    input[type=range]::-moz-range-track {
        width: 100%;
        height: 8.4px;
        cursor: pointer;
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
        background: #3071a9;
        border-radius: 1.3px;
        border: 0.2px solid #010101;
    }

    input[type=range]::-ms-track {
        width: 100%;
        height: 8.4px;
        cursor: pointer;
        background: transparent;
        border-color: transparent;
        border-width: 16px 0;
        color: transparent;
    }
    input[type=range]::-ms-fill-lower {
        background: #2a6495;
        border: 0.2px solid #010101;
        border-radius: 2.6px;
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
    }
    input[type=range]:focus::-ms-fill-lower {
        background: #3071a9;
    }
    input[type=range]::-ms-fill-upper {
        background: #3071a9;
        border: 0.2px solid #010101;
        border-radius: 2.6px;
        box-shadow: 1px 1px 1px #000000, 0px 0px 1px #0d0d0d;
    }
    input[type=range]:focus::-ms-fill-upper {
        background: #367ebd;
    }

    label {
        font-size: 1em;
        font-weight: normal;
        display: inline-block;
        width: 50px;
    }   
    h1 {
        text-align: center;
    }


    .sentence {
        background: #9bc5aa;
        padding: 25px 20px;
        position: relative;
        margin-bottom: 5px;
        margin-left: 5px;
        margin-top: 5px;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 10px;
        font-size: 1.4em;
        color: #4a1710;    
        height: 100%;
    }    

    /* accordion help menu */

    /* Style the buttons that are used to open and close the accordion panel */
    button.accordion {
        background-color: #9bc5aa;
        color: #4a1710;
        cursor: pointer;
        width: 100%;
        text-align: left;
        border: none;
        padding:18px;
        outline: none;
        transition: 0.4s;
        font-size: 1.4em;
        opacity:1;
        /*border-radius: 10px;*/ 
    }

    /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
    button.accordion.active, button.accordion:hover {
        background-color: #BEDAC8;
    }

    /* Style the accordion panel. Note: hidden by default */
    div.panel {
        padding: 12px 18px;
        background-color: transparent;
        color: white;
        display: none;
        margin-bottom: 0px;
        border-color: #9bc5aa;
        border-radius: 0px;
    }

    /* The "show" class is added to the accordion panel when the user clicks on one of the buttons. This will show the panel content */
    div.panel.show {
        display: block;
    }

    button.accordion:after {
        content: '\02795';  Unicode character for "plus" sign (+) 
        font-size: 13px;
        color: #777;
        float: right;
        margin-left: 5px;
    }

    button.accordion.active:after {
        content: "\2796";  /* Unicode character for "minus" sign (-) */
    }

    #main {
        width : 100%;
        float : left;
        position : relative;
        padding-top : 15px;
        padding-left : 15px;
        padding-right : 15px
    }

    #right {
        position : relative;
        padding-top : 15px;
        padding-left : 15px;
        padding-right : 15px
    }
    /*    .wrapper {
            position: relative;
        }*/



    #help {
        width : 0%;
        /*        border-radius : 15px;*/
        display : none;
        overflow-y : auto;
        position : relative;
        /*        border-style : solid;
                border-color : #9bc5aa;*/
    }

    sub {
        vertical-align:text-bottom;
    }



    a.leftlabel,a.rightlabel:hover{
        text-decoration: none;
        color:white;
    }

    .leftlabel, .rightlabel {
        display: inline-block;
        width: 50px;
        padding: 0px;
        text-align: center;
        cursor: pointer;
        color: white;

        .leftlabel {
            background-color: red;
            color: white;
            margin-right: 1em;
        }
        .rightlabel {
            background-color: purple;
            color: white;
            margin-left: 1em;
        }
    }


    élément {
        color: rgb(96, 96, 96);
        cursor: default;
        font-size: 11px;
        fill: rgb(96, 96, 96);
    }


    button.btn_unk{
        border-width:0px;
        border-radius: 10px;
        font-size:inherit; 
        border-width: 0px;
        background-color:white;
        color:black 
    }
    button.btn_unk:hover{
        color:black;
        background-color:white;
    }
    button.btn_unk:visited{
        color:black;
        background-color:white;
    }
    button.btn_unk:active, button.btn_unk.active{
        /*        background: #2e618d;*/
        border-width: 1px;
        border-color: green;
        background-color:white;
        color:black;
        box-shadow: inset -1px 1px 5px 0 #16334d;
    }


    select.select_pos{
        background-color:#9bc5aa;
        border-width:0px;
        border-radius: 10px;
        font-size:inherit; 
        border-width: 0px; 
        padding : 5px;
        background-color: white; 
        color:black 
    }

    select.select_pos.active{
        /*        background: #2e618d;*/
        border-width: 1px;
        border-color: green;
        box-shadow: inset -1px 1px 5px 0 #16334d;
    }
</style>

@stop

@section('scripts')
{!! Html::script('js/highcharts.js') !!}
<script>

    /* get sentence ids */
    ids = {!! json_encode($ids) !!};
    /*
     //    $('input[name=aname]').on('change', function () { alert(this.value)});
     //        alert($('input[name=aname]').value);
     
     //    $.each(ids, function (key, valeur) {
     //    $('input[name=aname]').on('change', function () { alert(this.value)});
     //
     //    }); 
     // unused
     //    function displayAltPos(id){
     //    $('#alt_pos_' + id).css("visibility", "visible");
     //    }
     
     // set checked property to true for "other..." option on this element <input type="radio" value="0" name="answer[{{ $annotation->id }}]" /> 
     //$(".select_pos").change(function () {
     //    if ($(this).val() !== 0)
     //        $(this).prev.prop("checked", true);
     //    else
     //        $(this).prev().prop("checked", false);
     //});
     */

    /* Tabs */
//    $.each(ids, function (key, valeur) {
//       
//        jQuery(function () {
//            jQuery('#tab_'+ valeur).tab('show')
//        })
//    });

    $(".select_pos").change(function () {
        annotation_id = $(this).prop('name').match(/[0-9]+/);
        if ($(this).val() != 0) {
            $("[name='answer[" + annotation_id + "]']").val(3);
            $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
            $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
            $('#unk_' + annotation_id).removeClass('active');
            $('#checked_' + annotation_id).css({'visibility': 'visible'});
            $('#checked_txt_' + annotation_id).html($('#other_' + annotation_id + ' option:selected').text());
        } else {
            $('#checked_' + annotation_id).css({'visibility': 'hidden'});
        }
    });
    $(".btn_unk").click(function () {
        annotation_id = $(this).prop('name').match(/[0-9]+/);
        $('#unk_' + annotation_id).toggleClass('active');
        if ($('#unk_' + annotation_id).hasClass('active')) {
            $('#other_' + annotation_id).val(0);
            $("[name='answer[" + annotation_id + "]']").val(3);
            $('#checked_' + annotation_id).css({'visibility': 'visible'});
            $('#checked_txt_' + annotation_id).html($('#unk_' + annotation_id).text());
        } else {
            $('#checked_' + annotation_id).css({'visibility': 'hidden'});
        }

    });
    $(".slide").change(function () {
        annotation_id = $(this).prop('name').match(/[0-9]+/);
        $('#other_' + annotation_id).val(0);
        $('#unk_' + annotation_id).removeClass('active');
        value = $("[name='answer[" + annotation_id + "]']").prop('value');
        if (value != 3) {
            $('#other_' + annotation_id).val(0); //css({'visibility':'hidden'});
            if (value == 1 || value == 2) {
                $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'bold'});
                $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
                $('#checked_' + annotation_id).css({'visibility': 'visible'});
                $('#checked_txt_' + annotation_id).html($("[name='leftlabel[" + annotation_id + "]']").text());
            } else {
                $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'bold'});
                $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
                $('#checked_' + annotation_id).css({'visibility': 'visible'});
                $('#checked_txt_' + annotation_id).html($("[name='rightlabel[" + annotation_id + "]']").text());
            }
            ;
        } else {
            $('#checked_' + annotation_id).css({'visibility': 'hidden'});
            $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
            $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
        }
        ;
        //        else
        //            $('#other_' + annotation_id).css({'visibility':'visible'});
    });
    /*
     //alert(document.getElementById("answer[" + annotation_id + "]").val());
     //       $("[id='answer[" + annotation_id + "]']").value = "3";
     //        $(this).prop("checked", false);
     
     
     
     
     // set radio button to true if otrher tag is selected
     //$("input[type='radio']").click(function () {
     //    if ($(this).val() !== 0) {
     //        var annotation_id = $(this).prop('name').match(/[0-9]+/);
     //        $('#other_' + annotation_id + ' option[value="0"]').prop('selected', true);
     //    }
     //    // alert(annotation_id);
     //    // 
     //});
     //    function create_charts_content(postag_names, postag_full_names, postag_descriptions) {
     //        var content = 'hello';
     //        return content;
     //    }
     */

    $(document).on('submit', "#form-pos", function (event) {
        var missing = false;
        event.preventDefault();
        $.each(ids, function (key, valeur) {
            /* find unchecked sentences */
            //nicolas' way
            //        var selected = $("input[type='radio'][name='answer[" + value + "]']:checked");  
            if ($("[name='answer[" + valeur + "]']").prop('value') == 3
                    && $('#other_' + valeur + ' option[value="0"]').prop('selected') == true
//                    ){ 
                    /* problem for sending button active value through form */
//                    && $('#unk_' + valeur).hasClass('active') == false) {
                    && $("input[type='radio'][name='unk_" + valeur + "']:checked") == false) {
                $("#line_" + valeur).css({'border-style': 'solid', 'border-color': 'red', 'border-width': '3px'});
                missing = true;
            } else {
                return;
            }

        });
        if (missing) {
            alert("Il faut sélectionner une réponse pour chacune des phrases !")
            return;
        }

        $.ajax({
            method: 'POST',
            url: base_url + 'pos-game/index',
            data: $(this).serialize(),
            complete: function (response) {

                $("#validation-button").css({'display': 'none'});
                $("#next-button").css({'display': 'block'});
                $("#stats-label").css({'display': 'block'});
                var cur_sentence = 0;
                $.each(ids, function (key, valeur) {
                    $("#line_" + valeur).css({'border-style': 'none'});
                    $(".cell").css({'display': 'none'});
                    var body = $(response.responseText).find('#chart_post_' + valeur).html();
                    cur_sentence = cur_sentence + 1;
                    $("#response-div").html(response.responseText);
                    $("#response-div").find("script").each(function (i) {
                        eval($(this).text());
                    });
                });
            }

        });
    });
    $(document).ready(function () {
        $(".sentence").each(function () {
            var sentence = displaySentence($(this).html(), $(this).attr('data-focus'), undefined, 'special');
            $(this).html(sentence);
        });
    });
    /* accordion help menu javascript */

    var acc = document.getElementsByClassName("accordion");
    var i;
    $(acc[0]).css({'border-top-left-radius': '15px', 'border-top-right-radius': '15px'});
    $(acc[acc.length - 1]).css({'border-bottom-left-radius': '15px', 'border-bottom-right-radius': '15px'});
    var toggle_acc = false;
    $(acc[acc.length - 1]).on('click', function () {
        if (toggle_acc == false) {
            $(acc[acc.length - 1]).css({'border-bottom-left-radius': '0px', 'border-bottom-right-radius': '0px'})
            toggle_acc = true;
        } else {
            $(acc[acc.length - 1]).css({'border-bottom-left-radius': '15px', 'border-bottom-right-radius': '15px'})
            toggle_acc = false;
        }
    });
    for (i = 0; i < acc.length; i++) {
        acc[i].onclick = function () {
            this.classList.toggle("active");
            this.nextElementSibling.classList.toggle("show");
        }
    }

    /* animation on click for help display */
    $('.help-trigger').on('hover', function () {
        $this.css({'display': 'none'});
    });
    var toggle = false;
    $('.help-trigger').on('click', function () {
        if (toggle == false) {
            $('#main').animate({width: "-=25%", }, 500, function () {
                $('#help').css({'width': '24%'});
                $('#help').show();
                $('#help').animate({opacity: 1}, 500);
                $('.full').hide();
                $('.shrinked').show();
            });
            toggle = true;
        } else {
            $('#help').animate({opacity: 0}, 500, function () {
                $('#help').css({'display': 'none'});
                $('#help').animate({width: "-=24%", }, 1000);
                $('#main').animate({width: "+=25%"}, 500);
                $('.shrinked').hide();
                $('.full').show();
            });
            toggle = false;
        }
    });
    /* make help block scrollable */
    var positionElementInPage = $('#help').offset().top;
    $(window).scroll(
            function () {
                if ($(window).scrollTop() >= positionElementInPage) {
                    // fixed
                    $('#help').addClass("floatable");
                    // relative
                } else {
                    $('#help').removeClass("floatable");
                }
            }
    );
    function reload() {
        location.reload();
    }

    $('.leftlabel').on('click', function () {
        annotation_id = $(this).prop('name').match(/[0-9]+/);
        $("[name='answer[" + annotation_id + "]']").val(1);
        $('#other_' + annotation_id).val(0);
        $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
        $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'bold'});
        $('#checked_' + annotation_id).css({'visibility': 'visible'});
        $('#checked_txt_' + annotation_id).html($("[name='leftlabel[" + annotation_id + "]']").text());
    });
    $('.rightlabel').on('click', function () {
        annotation_id = $(this).prop('name').match(/[0-9]+/);
        $("[name='answer[" + annotation_id + "]']").val(5);
        $('#other_' + annotation_id).val(0);
        $("[name='rightlabel[" + annotation_id + "]']").css({'font-weight': 'bold'});
        $("[name='leftlabel[" + annotation_id + "]']").css({'font-weight': 'normal'});
        $('#checked_' + annotation_id).css({'visibility': 'visible'});
        $('#checked_txt_' + annotation_id).html($("[name='rightlabel[" + annotation_id + "]']").text());
    });


</script>
@yield('javascript')
@stop
