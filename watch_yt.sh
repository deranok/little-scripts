#!/usr/bin/env python3
# Watch youtube conveniently
from yt_dlp import YoutubeDL
import sys
import json
import pprint
import subprocess

dl_params = {
   # 'simulate': True,
   # 'listformats': True,
   'format': '-',
   'outtmpl': {'default': '[%(channel)s] %(title)s.%(ext)s'},
   'nopart': True,
   'retries': float('inf'),
   'retry_sleep_functions': {
      'http': lambda n: 4,
      'fragment': lambda n: 1,
      'file_access': lambda n: 1
   },
}

with YoutubeDL(dl_params) as ydl:
    my_info = ydl.extract_info(sys.argv[1], download=False)

    vid_url = False
    aud_url = False
    for ydl_format in my_info['requested_formats']:
        if ydl_format['vcodec'] != 'none':
            vid_url = ydl_format['url']
        if ydl_format['acodec'] != 'none':
            aud_url = ydl_format['url']

    vid_title = my_info.get('fulltitle', 'YT Video')
    if vid_url and aud_url:
        subprocess.run(['mpv',
                        '--player-operation-mode=pseudo-gui',
                        f'--title={vid_title}',
                        '--keep-open=yes',
                        f'--audio-file={aud_url}',
                        vid_url])
