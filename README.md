TiebaSign
=========

php script with php.exe .


Usage
---
Create file: conn.php, and connect the mysql server to variable: $con .  
Create a table(named tb_user) in a mysql database (such as test);  
This table has four fields: `id`(A_I), `desc`(user description; nickname), `cookies`(at wapp.baidu.com) and `last`.  
Type: int, varchar(255), varchar(1024), varchar(50)  
Save your 'baidu tieba' account info here.  

Then open the php in cosole mode  
(Manual: )    
e.g. in windows:(if the folder of php.exe __IS in your PATH__ )    
`php exectbs.php cachetb` to cache tieba list first, then:   
`php exectbs.php`    
(Auto: )   
use windows.cpp (P.S. it's for windows);in linux can use croontab.   


That's all.  

License
---
The MIT License (MIT)  

Copyright (c) 2013 Senor(  zhs490770@foxmail.com  )

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
