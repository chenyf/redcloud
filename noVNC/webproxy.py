#!/usr/bin/python

import sys
import hashlib

class  webproxy:
        token = ''
        tokenhtml = ''
        my_vm = ''
	gw_ip = ''
        def __init__(self,vm_ip,gw_ip,port):
                self.my_vm = vm_ip
		self.gw_ip = gw_ip
                self.token = vm_ip.split(".")[-1]
                m = hashlib.md5()
                m.update(self.token)
                self.tokenhtml = m.hexdigest()
        
                f1 = open('/data/cloudssd/noVNC/vnc_tokens/'+self.token,'w')
                f1.write(self.token+':'+' '+self.gw_ip+':'+port)
                f1.close()
        def get_url(self):
                try:
                        f2 = open('/data/cloudssd/noVNC/template_vnc.html')
                        new_html = '/data/cloudssd/noVNC/vnc_html/'+self.tokenhtml+'.html'
                        f3 = open(new_html,'w')
                        f3.write(f2.read().replace('******',self.token).replace('||||||',self.gw_ip))
                        f2.close()
                        f3.close()
                        print(self.my_vm+' '+'webproxy success!')
                except Exception, e:
                        print e
                        print(self.my_vm+' '+'webproxy failed!,possible has been proxy!')


if __name__ == '__main__':
        proxy = webproxy(sys.argv[1],sys.argv[2],sys.argv[3])
        proxy.get_url()

