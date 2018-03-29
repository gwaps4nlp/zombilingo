#!/usr/bin/python
# -*- coding: utf-8 -*-

# This script is a filter to extract plain texte article from xml files in the "Est Républiain" corpus

from BeautifulSoup import BeautifulSoup
import codecs
import sys
import re

# ================================================================================
# expect one argument: the basename
if len(sys.argv) != 2:
	print ("Usage: ER-filter.py basename")
	sys.exit (2)
basename = sys.argv[1]
print ("==[ER-filter]==> basename = " + basename )

# ================================================================================
# read the file "basename.xml" as an utf-8 string
f = codecs.open(basename+".xml", "r", "utf-8")
full = f.read()
f.close ()

# ================================================================================
# XML parsing
xml = BeautifulSoup(full)

# We keep only xml element with attribute type=article, i.e. the real article
article_list = xml.findAll(type="article")
print ("==[ER-filter]==> Found " + `(len (article_list))` + " articles")

for (index,article) in enumerate(article_list):
	# we keep only the ones with a "legende" which are supposed to be more interesting 
	if article.findAll(type="legende"):
		# contents on the elements tagged "head" or "p" are written in a separate file for each article 
		out = codecs.open(basename+"_"+(str(index).zfill(4))+".txt", "w", "utf-8")
		for item in article.findAll (lambda tag: tag.name=="p" or tag.name =="head"):
			text = item.contents[0]
			text = re.sub (r'^ +', "", text)
			text = re.sub (r'\n +', " ", text)
			# Talismane turns '« ' and ' »' into '"'
			text = re.sub (u'« ?', '"', text)
			text = re.sub (u' ?»', '"', text)
			out.write (text+"\n")
		out.close()
