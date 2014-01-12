#!/usr/bin/python
# coding:utf-8
import smtplib
from email.mime.text import MIMEText
import sys
#mail_to_list = ['lihuipeng@xxx.com',]
mail_host = ‘smtp.163.com’
mail_user = ‘testest’  # 发件人邮箱帐号
mail_pass = ’123456′  # 发件人邮箱密码
mail_postfix = ’163.com’


def send_mail(to_list, subject, content):
    me = mail_user +”<”+mail_user +”@”+mail_postfix +”>”
    msg = MIMEText(content)
    msg['Subject'] = subject
    msg['From'] = me
    msg['to'] = “
    ”.join(to_list)
    #msg['to'] = to_list

    try:
        s = smtplib.SMTP()
        s.connect(mail_host)
        s.login(mail_user, mail_pass)
        s.sendmail(me, to_list, msg.as_string())
        s.close()
        return True
    except Exception, e:
        print str(e)
        return False

if __name__ == “__main__”:
    print sys.argv[1]
    print sys.argv[2]
    print sys.argv[3]
    send_mail(sys.argv[1], sys.argv[2], sys.argv[3])
