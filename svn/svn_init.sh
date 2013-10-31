#/bin/bash

#install svn
sudo apt-get install subversion -y
sudo apt-get install subversion-tools -y

#add the SVN admin user and subversion group
sudo adduser svnuser <<EOF






EOF

sudo addgroup subversion
sudo addgroup svnuser subversion

#mkdir the project directory
sudo mkdir /home/svn/project -p
sudo chown -R root.subversion /home/svn/project
sudo chmod -R g+rws /home/svn/project

#make the repository with svnadmin
sudo svnadmin create /home/svn/project

#change the right on repository project
sudo chmod -R 700 /home/svn/project

#change the conf under the /home/svn/project/conf/
#configure the svnserver.conf
echo "anon-access=none"     >> /home/svn/project/conf/svnserve.conf
echo "auth-access=write"    >> /home/svn/project/conf/svnserve.conf
echo "password-db=password" >> /home/svn/project/conf/svnserve.conf
echo "authz-db=authz"       >> /home/svn/project/conf/svnserve.conf

#configure the passwd
echo "admin=admin"			>> /home/svn/project/conf/passwd
echo "user=user"			>> /home/svn/project/conf/passwd

#configure the authz
echo "admin=admin"			>> /home/svn/project/conf/authz
echo "user=user"			>> /home/svn/project/conf/authz
echo "[/]"					>> /home/svn/project/conf/authz
echo "@admin=rw"			>> /home/svn/project/conf/authz
echo "*=r"					>> /home/svn/project/conf/authz


#start the svn services
sudo svnserve -d -r /home/svn


#install apache server
sudo apt-get install apache2 -y
sudo apt-get install libapache2-svn -y




