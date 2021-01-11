import subprocess

#run mmcli
l = subprocess.run(["mmcli", "-L"], text=True, capture_output=True)
print(l.stdout)
l = l.stdout.split(" ")
l = l[4]
l = l.split("/")
l = l[5]

cmd = " --simple-enable=\"apn=browse\",\"user=\",\"password=\""
l = subprocess.run(["mmcli", "-m", l, "--simple-connect=apn=browse,user=,password="], text=True, capture_output=True)
print(l.stdout)
l = subprocess.run(["sudo", "ip", "link", "set", "wwan0", "up"], text=True, capture_output=True)
print(l.stdout)
l = subprocess.run(["sudo", "dhclient", "-4", "wwan0"], text=True, capture_output=True)
print(l.stdout)
