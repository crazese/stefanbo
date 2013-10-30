#-*- encoding: gb2312 -*-
import HTMLParser

class MyParser(HTMLParser.HTMLParser):
    def __init__(self):
        HTMLParser.HTMLParser.__init__(self)        

        
    def handle_starttag(self, tag, attrs):
        # 这里重新定义了处理开始标签的函数
        if tag == 'a':
            # 判断标签的属性
            for name,value in attrs:
                if name == 'href':
                    print value
       

if __name__ == '__main__':
    a = 'http://www.163.com'
    
my = MyParser()


my.feed(a)