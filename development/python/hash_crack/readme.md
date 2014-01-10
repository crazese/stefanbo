在渗透测试的过程中，我们常常会dump出用户密码的哈希值。
每一个渗透测试人员都会使用不同方法破解哈希值以便获得权限或用于进一步渗透。
初步的渗透测试结束后，拥有一个有效的密码将会为我们在服务器或域环境中做进一步渗透争取更多的时间
出于这个原因，我编写了此脚本，整合了互联网中现有的破解各类Hash的服务。

当前支持的哈希类型：
--------
  MD4 - RFC 1320
  MD5 - RFC 1321
  SHA1 - RFC 3174 (FIPS 180-3)
  SHA224 - RFC 3874 (FIPS 180-3)
  SHA256 - FIPS 180-3
  SHA384 - FIPS 180-3
  SHA512 - FIPS 180-3
  RMD160 - RFC 2857
  GOST - RFC 5831
  WHIRLPOOL - ISO/IEC 10118-3:2004
  LM - Microsoft Windows hash
  NTLM - Microsoft Windows hash
  MYSQL - MySQL 3, 4, 5 hash
  CISCO7 - Cisco IOS type 7 encrypted passwords
  JUNIPER - Juniper Networks $9$ encrypted passwords
  LDAP_MD5 - MD5 Base64 encoded
  LDAP_SHA1 - SHA1 Base64 encoded


破解单个哈希值：
---------
  python findmyhash.py MD5 -h "098f6bcd4621d373cade4e832627b4f6"
  
如果不能破解，将会使用google搜索引擎。
---------
  python findmyhash.py SHA1 -h “A94A8FE5CCB19BA61C4C0873D391E987982FBBD3″-g

去破解一个哈希值文件（每行一个哈希）：
---------
  python findmyhash.py MYSQL -f mysqlhashesfile.txt

其他的例子：
----------
  python findmyhash.py MD4 -h "db346d691d7acc4dc2625db19f9e3f52"
  
  python findmyhash.py MD5 -h "098f6bcd4621d373cade4e832627b4f6"
  
  python findmyhash.py SHA1 -h "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3"
  
  python findmyhash.py SHA224 -h "90a3ed9e32b2aaf4c61c410eb925426119e1a9dc53d4286ade99a809"
  
  python findmyhash.py SHA256 -h "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08"
  
  python findmyhash.py SHA384 -h  "768412320f7b0aa5812fce428dc4706b3cae50e02a64caa16a782249bfe8efc4b7ef1ccb126255d196047dfedf17a0a9"
  
  python findmyhash.py SHA512 -h "ee26b0dd4af7e749aa1a8ee3c10ae9923f618980772e473f8819a5d4940e0db27ac185f8a0e1d5f84f88bc887fd67b143732c304cc5fa9ad8e6f57f50028a8ff"
  
  python findmyhash.py RMD160 -h "5e52fee47e6b070565f74372468cdc699de89107"
  
  python findmyhash.py GOST -h "a6e1acdd0cc7e00d02b90bccb2e21892289d1e93f622b8760cb0e076def1f42b"
  
  python findmyhash.py WHIRLPOOL -h "b913d5bbb8e461c2c5961cbe0edcdadfd29f068225ceb37da6defcf89849368f8c6c2eb6a4c4ac75775d032a0ecfdfe8550573062b653fe92fc7b8fb3b7be8d6"
  
  python findmyhash.py LM -h "01fc5a6be7bc6929aad3b435b51404ee:0cb6948805f797bf2a82807973b89537"
  
  python findmyhash.py LM -h "01fc5a6be7bc6929aad3b435b51404ee"
  
  python findmyhash.py NTLM -h "01fc5a6be7bc6929aad3b435b51404ee:0cb6948805f797bf2a82807973b89537"
  
  python findmyhash.py NTLM -h "0cb6948805f797bf2a82807973b89537"
  
  python findmyhash.py MYSQL -h "378b243e220ca493"
  
  python findmyhash.py MYSQL -h "*94bdcebe19083ce2a1f959fd02f964c7af4cfc29"
  
  python findmyhash.py MYSQL -h "94bdcebe19083ce2a1f959fd02f964c7af4cfc29"
  
  python findmyhash.py CISCO7 -h "12090404011C03162E"
  
  python findmyhash.py JUNIPER -h "\$9\$90m6AO1EcyKWLhcYgaZji"
  
  python findmyhash.py LDAP_MD5 -h "{MD5}CY9rzUYh03PK3k6DJie09g=="
  
  python findmyhash.py LDAP_SHA1 -h "{SHA}qUqP5cyxm6YcTAhz05Hph5gvu9M="
