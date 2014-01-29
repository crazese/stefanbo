set "ymd=%date:~,4%%date:~5,2%%date:~8,2%"
rsync -av /cygdrive/e/美术库 rsync://192.168.1.120:52326/backup/

cd F:\cwrsync\backup
winrar.exe a -ag -k -r -s -ibck F:\cwrsync\backup\rar_back\美术库.rar 美术库

forfiles /p "F:\cwrsync\backup\rar_back" /s /m *.rar /d -30 /c "cmd /c del @path"

