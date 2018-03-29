<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />

        {!! Html::style('css/back/css/bootstrap.css') !!}
     
		@yield('css')
    <script>
      function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        var arrFrames = parent.parent.document.getElementsByTagName("IFRAME");
        for (var i = 0; i < arrFrames.length; i++) {
          if (arrFrames[i].name != obj.name) {
            resizeIframe(arrFrames[i]);
            }
        }
      }
      function resizeIframe2(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        var arrFrames = parent.parent.parent.document.getElementsByTagName("IFRAME");
        for (var i = 0; i < arrFrames.length; i++) {
          if (arrFrames[i].name != obj.name) {
            resizeIframe2(arrFrames[i]);
            }
        }
      }
      function resizeIframeAgain(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        var arrFrames = parent.document.getElementsByTagName("IFRAME");
        for (var i = 0; i < arrFrames.length; i++) {
          if (arrFrames[i].name != obj.name) {
            resizeIframeAgain(arrFrames[i]);
            }
        }
      }
    </script>
        <script src="{{ asset(mix("build/js/app.js")) }}"></script>    
    </head>
    <body class="{{ App::environment('local')?'test':'' }}">
		@yield('main')

		@yield('scripts')
    </body>
</html>
