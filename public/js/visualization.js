var bratLocation = base_url + 'brat';

// var webFontURLs = [
//     bratLocation + '/static/fonts/Astloch-Bold.ttf',
//     bratLocation + '/static/fonts/PT_Sans-Caption-Web-Regular.ttf',
//     bratLocation + '/static/fonts/Liberation_Sans-Regular.ttf'
// ];
var webFontURLs = [];
   var collData = {
	'entity_types': [
// this is optional
        {
            'type': 'SPAN_DEFAULT',
            'labels': [ 'token', 'tok' ],
            'bgColor': '#7fa2ff',
            'borderColor': 'darken'
        },
        {
            "type": "",
            "labels": [ "" ],
            "bgColor": "green",
            "borderColor": "darken"
        }        
	],
	'entity_attribute_types': [],
	'relation_types': [
// this is optional
        // {
        //     'type': '',
        //     'labels': [ '' ],
        //     'dashArray': '3,3',
        //     'color': 'green',
        //     'args': [
        //         {
        //             'role': 'arg1',
        //             'targets': [ 'token' ]
        //         },
        //         {
        //             'role': 'arg2',
        //             'targets': [ 'token' ]
        //         }
        //     ]
        // }
        ],
	'event_types': [],
    };

    var normalizeSpace = function(s) {
	s = s.replace(/^\s+/, '');
	s = s.replace(/\s+$/, '');
	s = s.replace(/\s\s+/g, ' ');
	return s;
    };

    var compactJSON = function(s) {
	        // remove (some) space from JSON string, giving a visually
	        // more compact (but equivalent and still pretty-printed)
	        // version.

		// replace any space with ' ' in non-nested curly brackets
		s = s.replace(/(\{[^\{\}\[\]]*\})/g, 
			      function(a, b) { return b.replace(/\s+/g, ' '); });
		// replace any space with ' ' in [] up to nesting depth 1
	// 	s = s.replace(/(\[(?:[^\[\]\{\}]|\[[^\[\]\{\}]*\])*\])/g, 
	// 		      function(a, b) { return b.replace(/\s+/g, ' '); });
		// actually, up to nesting depth 2
		s = s.replace(/(\[(?:[^\[\]\{\}]|\[(?:[^\[\]\{\}]|\[[^\[\]\{\}]*\])*\])*\])/g, 
			      function(a, b) { return b.replace(/\s+/g, ' '); });
		return s
    };

    var ObjectToString = function(data) {
		return compactJSON(JSON.stringify(data, undefined, '    '));
    };

    var parseToken = function(token) {
		// return [text, POS] for token encoded as "text/POS", or
		// [text, "token"] if no POS (/-separated string) is included.
		var text, POS;

		m = token.match(/((?:[^\\]|\\.)+)\/(.+)$/);
		if (!m) {
		    text = token, POS = 'token';
		} else {
		    text = m[1], POS = m[2];
		}

		// unescape backslash escapes in text and tag
		text = text.replace(/([^\\]*)\\(.)/g, '$1$2');
		POS = POS.replace(/([^\\]*)\\(.)/g, '$1$2');

		return [text, POS];
    };

    // parse SD format, return brat document data format
    var sdParse = function(sd, logElement, annotation_id) {
		var log = function(s) {
		    if (logElement === undefined) {
			console.log(s);
		    } else {
			logElement.append(s+'\n');
		    }
		};

		var lines = sd.split('\n');
		var focus = data_brat[annotation_id]['focus'];
		var type_focus = data_brat[annotation_id]['type-focus'];
		// first line is assumed to be sentence text
		var text = lines[0];
		text = normalizeSpace(text);
		var answers = data_brat[annotation_id]['answers'];
		// determine token offsets and construct spans ("entities")
		var tokens = text.split(' ');
		var spans = [];
		var offsets = [];
		var styles = [];
		var offset = 0;
		for (var i=1; i<=tokens.length; i++) {
		    var text_POS = parseToken(tokens[i-1]);
		    var text = text_POS[0], POS = text_POS[1];
		    var length = text.length;
		    if(i==focus)
		    	spans.push(['T'+i, '', [[offset, offset+length]]]);
		    tokens[i-1] = text;
		    offsets[i] = {offset : offset, length: length};
		    offset += length + 1;
		}

		for (var i=0; i<answers.length; i++) {
			var answer = answers[i];
			if(answer.dependent!=99999 && answer.governor!=99999){
				if(type_focus=='governor')
					var word_position = answer.dependent;
		    	else
		    		var word_position = answer.governor;
		    	try {
		    		spans.push(['T'+(word_position), answer.percent+'%', [[offsets[word_position].offset, offsets[word_position].offset+offsets[word_position].length]]]);
		    	} catch (e) {
		    		
		    	}
			}
		}

		text = tokens.join(' ');

		// parse lines after the first as dependencies, construct relations
		var relations = [];
		for (var i=0; i<answers.length; i++) {
		    var annotation = answers[i];
		    relations.push([ 'R'+i, annotation.relation, [ [ 'arg1', 'T'+(annotation.governor) ], 
						    [ 'arg2', 'T'+(annotation.dependent)   ] ] ]);
		}

		log('SD parse done: '+spans.length+' tokens, '+relations.length+' dependencies.');

		return {
		    'text': text,
		    'entities' : spans,
		    'relations' : relations,
		    'styles' : styles
		};

    };

    var embeddedIdSeq = 1;
    var annot_ids = [];
    var embedStanfordDependency = function(elem, data) {


    annotation_id = elem.attr('id').match(/[0-9]+/);
    annot_ids.push(annotation_id);
	elem.attr('embeddedSequenceNum', embeddedIdSeq);

	var eId = 'embedded-' + embeddedIdSeq++,
            sdTabId = eId + '-2',
            bratTabId = eId + '-3',
            infoTabId = eId + '-4';

	// visualization and related data elements
	var visDiv = $('<div id="'+eId+'-vis"></div>');

	var logInput = $('<textarea id="'+eId+'-log" disabled="disabled" class="embedded-brat-data"></textarea>');
	var bratInput = $('<textarea id="'+eId+'-brat" disabled="disabled" class="embedded-brat-data"></textarea>');
	// initialize data, defaulting to original element text
	if (data === undefined) {
	    data = elem.text();
	}
	data = normalizeSpace(data);

	var parsed = sdParse(data, logInput, annotation_id);

	// build top-level structure
	elem.empty();

	elem.append( visDiv);

	// initialize brat visualization
        var dispatcher = Util.embed(eId+'-vis',
          $.extend({'collection': null}, collData),
          $.extend({}, parsed), webFontURLs);

	// hook everything up
        var renderError = function() {
            // bratInput.css({'border': '2px solid red'});
        };
        dispatcher.on('renderError: Fatal', renderError);
        dispatcher.on('unspin', function(){
        	setTimeout(highlightAnswers,500);
        });

        var inputHandler = function() {
            var parsed;

	    logInput.val(''); // clear log

            try {
                parsed = sdParse(sdInput.val(), logInput);
                // sdInput.css({'border': '2px inset'});
            } catch (e) {
                // sdInput.css({'border': '2px solid red'});
                return;
            }
	    
	    bratInput.text(ObjectToString(parsed));

            try {
                // dispatcher.post('requestRenderData', [$.extend({}, parsed)]);
                bratInput.css({'border': '2px inset'});
            } catch(e) {
				console.log('requestRenderData error:', e);
				logInput.append('requestRenderData error: '+e);
                bratInput.css({'border': '2px solid red'});
            }
        };
        return dispatcher;


    };
    var highlightAnswers = function() {
	    var annot_id = annot_ids.shift();
        var answers = data_brat[annot_id]['answers'];
        var focus = data_brat[annot_id]['focus'];
        var type_focus = data_brat[annot_id]['type-focus'];

        $("#annotation_"+annot_id+" tspan[data-chunk-id='"+(focus-1)+"']").addClass("highlight");

		for (var i=0; i<answers.length; i++) {
			var answer = answers[i];
			
			if(answer.dependent!=99999 && answer.governor!=99999){

				if(type_focus=='dependent')
					var position = answer.governor;
		    	else
		    		var position = answer.dependent;

		    	$("#annotation_"+annot_id+" rect[data-span-id='T"+position+"']").addClass(answer.label);

			}
		}
    }
    var resolveEmbeddedReference = function(elem, data) {
	var refId = elem.attr('href'),
	    refElem = $(refId);
	
	if (refElem === undefined) {
	    console.log('Failed to resolve reference to', refId, 'for', elem);
	    return;
	}

	var refSeq = refElem.attr('embeddedSequenceNum');
	if (refSeq === undefined) {
	    console.log('no embeddedSequenceNum for', refElem);
	    return;
	}

	var origText = elem.text();
	var resolvedText = origText.replace(/\#/, refSeq);
	if (origText === resolvedText) {
	    console.log('failed replace in text', origText, 'for', elem);
	    return;
	}

	elem.text(resolvedText);
    };

    var embedBratVisualizations = function() {
        $('.sentence-brat').each(function(idx) {
		    embedStanfordDependency($(this));
		});

		$('.embed-ref').each(function(idx) {
	        resolveEmbeddedReference($(this));
		});
    };