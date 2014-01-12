#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb


def read_image():
    
    fin = open("woman.jpg")    
    img = fin.read()
    
    return img
    

con = mdb.connect('localhost', 'testuser', '123456', 'testdb')
 
with con:
    
    cur = con.cursor()
    data = read_image()
    cur.execute("INSERT INTO images VALUES(1, %s)", (data, ))