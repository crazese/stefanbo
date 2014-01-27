#!/usr/bin/env python

import socket
import sys
import threading
import time

streams = [None, None]  
debug = 1  

def _usage():
        print 'Usage: ./rtcp.py stream1 stream2\nstream : l:port  or c:host:port'

def _get_another_stream(num):
        if num == 0:
                num = 1
        elif num == 1:
                num = 0
        else:
                raise "ERROR"

        while True:
                if streams[num] == 'quit':
                        print("can't connect to the target, quit now!")
                        sys.exit(1)

                if streams[num] != None:
                        return streams[num]
                else:
                        time.sleep(1)

def _xstream(num, s1, s2):
        try:
                while True:
                        buff = s1.recv(1024)
                        if debug > 0:
                                print num,"recv"
                        if len(buff) == 0: 
                                print num,"one closed"
                                break
                        s2.sendall(buff)
                        if debug > 0:
                                print num,"sendall"
        except :
                print num,"one connect closed."

        try:
                s1.shutdown(socket.SHUT_RDWR)
                s1.close()
        except:
                pass

        try:
                s2.shutdown(socket.SHUT_RDWR)
                s2.close()
        except:
                pass

        streams[0] = None
        streams[1] = None
        print num, "CLOSED"

def _server(port, num):
        srv = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        srv.bind(('0.0.0.0', port))
        srv.listen(1)
        while True:
                conn, addr = srv.accept()
                print "connected from:", addr
                streams[num] = conn  
                s2 = _get_another_stream(num)  
                _xstream(num, conn, s2)

def _connect(host, port, num):
        not_connet_time = 0
        wait_time = 36
        try_cnt = 199
        while True:
                if not_connet_time > try_cnt:
                        streams[num] = 'quit'
                        print('not connected')
                        return None

                conn = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                try:
                        conn.connect((host, port))
                except Exception, e:
                        print ('can not connect %s:%s!' % (host, port))
                        not_connet_time += 1
                        time.sleep(wait_time)
                        continue

                print "connected to %s:%i" % (host, port)
                streams[num] = conn  
                s2 = _get_another_stream(num) 
                _xstream(num, conn, s2)


if __name__ == '__main__':
        if len(sys.argv) != 3:
                _usage()
                sys.exit(1)
        tlist = []  
        targv = [sys.argv[1], sys.argv[2] ]
        for i in [0, 1]:
                s = targv[i]  
                sl = s.split(':')
                if len(sl) == 2 and (sl[0] == 'l' or sl[0] == 'L'):  
                        t = threading.Thread(target=_server, args=(int(sl[1]), i))
                        tlist.append(t)
                elif len(sl) == 3 and (sl[0] == 'c' or sl[0] == 'C'):  
                        t = threading.Thread(target=_connect, args=(sl[1], int(sl[2]), i))
                        tlist.append(t)
                else:
                        _usage()
                        sys.exit(1)

        for t in tlist:
                t.start()
        for t in tlist:
                t.join()
        sys.exit(0)