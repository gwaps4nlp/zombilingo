open Printf

let jar_file = "talismane-fr-1.8.5b-allDeps.jar"
let code_folder = Filename.dirname Sys.executable_name

let _ =
  if not (Sys.file_exists (Filename.concat code_folder jar_file))
  then (
    printf "There is no file \"%s\" in the \"%s\" folder, cannot go on!\n%!" jar_file code_folder;
    exit 1 
  )

let args = Array.to_list Sys.argv

let usage () = printf "Usage: \n run_talismane [-n] basename\n"; exit 0

let dry_run = ref false
let keep_aux = ref false

let run_command command =
  printf " ----> %s\n%!" command;
  if not !dry_run
  then match Sys.command command with
    | 0 -> ()
    | n -> printf " ====> FAILED: '%s'\n%!" command; exit n


let clean basename =
	run_command (sprintf "rm -f %s.seq %s.sent %s.tal %s.unix" basename basename basename basename)

let purge basename =
	clean basename;
	run_command (sprintf "rm -f %s.conll" basename)

let basename = match args with
  | [_; "-n"] -> usage ()
  | [_; p] -> p
  | [_; "-n"; p] -> dry_run := true; p
  | [_; "-k"; p] -> keep_aux := true; p
  | [_; "-clean"; p] -> clean p; exit 0
  | [_; "-purge"; p] -> purge p; exit 0
  | _ -> usage ()


(* ==================================================================================================== *)

(* run Talismane to split into sentence *)
let _ = run_command (sprintf "java -Xmx1024M -jar %s command=analyse endModule=sentence < %s.txt > %s.sent" (Filename.concat code_folder jar_file) basename basename)

(* run Talismane to parse sentence *)
let _ = run_command (sprintf "java -Xmx1024M -jar %s command=analyse startModule=tokenise < %s.sent > %s.tal" (Filename.concat code_folder jar_file) basename basename)

(* run dos2unix *)
(* let _ = run_command (sprintf "dos2unix %s.tal" basename) *)
(* dos2unix not installed on cluster talc. Perl is used instead *)
let _ = run_command (sprintf "perl -ne 's/\\x0D\\x0A/\\x0A/g; print' %s.tal > %s.unix" basename basename)

(* run sed script *)
let _ = run_command (sprintf "sed -f %s %s.unix > %s.seq" (Filename.concat code_folder "tal2seq.sed") basename basename)

(* add sentid *)
let _ = run_command (sprintf "python %s %s" (Filename.concat code_folder "add_sentid.py") basename)

let _ = if not !keep_aux then clean basename