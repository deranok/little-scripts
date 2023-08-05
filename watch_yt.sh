vid_storage='/path/to/video/storage';
cur_d="$(pwd)";
cd $vid_storage;
  vid_id=$(echo $1 | cut -d ? -f 2 | cut -d '&' -f 1);
  vid_url='https://youtube.com/watch?'$vid_id;
  on_args='"%(channel)s - %(title)s.%(ext)s"';
  sel_frmt='b[height>=360]/b';
  sor_frmt='+size';
  fn_args=( -s -f "$sel_frmt" -S "$sor_frmt" -O "${on_args}" );
  dl_args=( -F $vid_url );
  out_file=$(yt-dlp "${fn_args[@]}" $vid_url); 
  if [ -z "$out_file" ]; then { cd "$cur_d"; return; } fi
  echo "output file: $out_file";
  yt-dlp -R "infinite" --no-part -o "$out_file" -f "$sel_frmt" -S "$sor_frmt" "$vid_url" &
  dl_pid=$!;
  function kill_dl() { cd "$cur_d"; kill $dl_pid; }
  trap kill_dl SIGINT;
  echo "waiting for download";
  until [ -e "$out_file" ]; do { sleep 5;  }; done
  echo -e "\rdownload started  ";
  tail -f -c +0 "$out_file" | mpv --pause -;
cd "$cur_d";
