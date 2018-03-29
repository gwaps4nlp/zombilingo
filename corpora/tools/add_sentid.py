#!/usr/bin/python

import codecs
import sys

verbose = False

def log (msg):
	if verbose:
		print ("[LOG:add_sentid.py] " + msg)

# ================================================================================
# argument must be the basename
if len(sys.argv) != 2:
	print ("Usage: add_sentid.py basename")
	sys.exit (2)

basename = sys.argv[1]
log ("basename = " + basename )

# ================================================================================
# read the file "basename.txt" as an utf-8 string
f = codecs.open(basename+".txt", "r", "utf-8")
full = f.read()
log ("utf-8 lenght = " + `len(full)`)
f.close ()

# ================================================================================
# read the file "basename.sent" as an list of utf-8 string (remove emty lines)
f = codecs.open(basename+".sent", "r", "utf-8")
all_lines = [line.rstrip('\n') for line in f.readlines()]
sents = [line for line in all_lines if len(line) > 0]
log ("number of sentences = " + `len(sents)`)
f.close ()

# ================================================================================
# read the file "basename.seq" as an list of utf-8 string
f = codecs.open(basename+".seq", "r", "utf-8")
conll_lines = [line.rstrip('\n') for line in f.readlines()]
log ("number of conll lines = " + `len(conll_lines)`)
f.close ()

# ================================================================================
# main loop
current_pos = 0   # the utf8-position in the string full where next sentences should be searched
sent_num = 0      # the next index of sentence to use

out = codecs.open(basename+".conll", "w", "utf-8")
for conll in conll_lines:
	sp=conll.split ("\t");
	if len (sp) == 10 and sp[0] == u'1':

		if sent_num >= len (sents):
 			print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
			print "!!! ERROR !!! not enough sentences"
			exit (1)

		# search the sentence positions in "full"
		sent = sents[sent_num]
	 	start = full.find (sent,current_pos)
 		end = start + len(sent)
 		sentid = "sentid="+basename + "_" + `start`+ "_" + `end`

 		if start == -1:
 			print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
			print "!!! ERROR !!! sentence not found"
			print "!!! sent = \""+sent+"\" "
			print "!!! current_pos = "+`current_pos`
			exit (1)

		#update for next iteration
		current_pos = end
		sent_num += 1

		#update the field 5: the feature stucture
		if sp[5] == u'_':
			sp[5] = sentid
		else:
			sp[5] = sp[5] + '|' + sentid


	# write the line in the conll output file
	out_line = "\t".join (sp)
	out.write (out_line+"\n")

log ("SUCCESS: number of sentences: "+`len(sents)`)

out.close ()
