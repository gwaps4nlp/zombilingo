var base_url = '{{ asset('') }}';
var translations = {
    'game.you-found-object' : "{{ trans('game.you-found-object') }}",
    'game.bad-answer' : "{{ trans('game.bad-answer') }}",
    'game.remaining-trials' : "{{ trans('game.remaining-trials') }}",
    'game.max-level' : "{{ trans('game.max-level') }}",
    'game.no-more-sentences' : "{{ trans('game.no-more-sentences') }}",
    'game.no-more-attempt' : "{{ trans('game.no-more-attempt') }}",
    'game.or' : "{{ trans('game.or') }}",
    'game.back-menu' : "{{ trans('game.back-menu') }}",
    'game.next-sentence' : "{{ trans('game.next-sentence') }}",
    'game.buy' : "{{ trans('game.buy') }}",
    'game.relation-ahead-you' : "{{ trans('game.relation-ahead-you') }}",
    'game.points-ahead' : "{{ trans('game.points-ahead') }}",
    'game.relation-behind-you' : "{{ trans('game.relation-behind-you') }}",
    'game.points-behind' : "{{ trans('game.points-behind') }}",
    'game.to-won-sentence' : "{{ trans('game.to-won-sentence') }}",
    'site.close' : "{{ trans('site.close') }}",
    'site.will-not-be-your-friend' : "{{ trans('site.will-not-be-your-friend') }}",
    'site.confirm-delete-account' : "{{ trans('site.confirm-delete-account') }}",    
};
var hahahahahaha = "test";
var img_croix_os = '{!! Html::imageNotRelationHere() !!}';
@if(Auth::check())
    var enemies = {!! Auth::user()->getListAcceptedFriends()->toJson() !!};
    var pending_enemies = {!! Auth::user()->getListPendingFriendRequests()->toJson() !!};
    var ask_enemies = {!! Auth::user()->getListAskFriendRequests()->toJson() !!};
@else
    var enemies = [];
    var pending_enemies = [];
    var ask_enemies = [];
@endif
id_relation_refused = {{ \App\Models\Relation::where('slug','not-exists')->first()->id }};

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
}; 
Number.prototype.formatScore = function() {
@if(app()->getLocale()=='fr')
    return this.format(0,3,'&#8239;',',');
@else
    return this.format(0,3,',','.');
@endif
};  
String.prototype.formatScore = function() {
return parseInt(this,10).formatScore();
}; 