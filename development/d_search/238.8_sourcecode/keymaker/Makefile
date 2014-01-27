all: obj
	@echo make all
	gcc -o keymaker-pp *.o

clean:
	#Clean up
	rm -f *.o
	rm -f keymaker-pp
	
install:
	@echo make $@
	cp keymaker-pp /usr/bin/keymaker-pp -f

obj:
	gcc -c *.c

