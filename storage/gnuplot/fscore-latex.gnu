set terminal latex
set output "fscore.tex"
set boxwidth 0.9 absolute
set style fill solid 1.00 border lt -1
set key inside right top vertical Right noreverse noenhanced autotitle nobox
set style histogram clustered gap 1 title textcolor lt -1
set datafile missing '-'
set style data histograms
set xtics border in scale 0,0 nomirror rotate by -45
set xtics  norangelimit
set xtics   ()
set title "F-score" 
set yrange [ 0.00000 : 1.20000 ] noreverse nowriteback
x = 0.0
i = 22

plot "../app/fscore.dat" using 2:xtic(1) ti col, for [i=3:5] '' using i ti col, 1 title ""
