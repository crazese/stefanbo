#!/bin/bash/env python
#18.1
import random
def sample_wr(population, _choose=random.choice):
	while True: yield _choose(population)

#生成50个小写ascii字母的随机字符串
import itertools
from string import ascii_lowercase
x = ''.join(itertools.slice(sample_wr(ascii_lowercase), 50))