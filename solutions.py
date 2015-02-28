#! /usr/bin/python

import sqlite3
import sys

conn = sqlite3.connect('lights.db')
#print sys.argv[1]
curs = conn.cursor()
#print conn.query("SELECT start FROM customers WHERE finish='%s'" % sys.argv[1]).getresult()[0][0]

#starts = conn.query('SELECT start FROM customers')
#for start in starts.getresult():
figure_for = 20
while figure_for < 21:
    curs.execute('CREATE TABLE IF NOT EXISTS final_%d (start text, finish text PRIMARY KEY)' % (figure_for))
    go = 1
    count = 0
    errors = 0
    while go < 2**figure_for:
        start = bin(go)[2:].zfill(figure_for)
        rows = {}
        rows[0] = list(start) # 00001
        for i in range(1,figure_for+1): # fill in the rest of the grid
            rows[i] = []
            for j in range(0,figure_for):
                rows[i].append('0')

        for i in range(1,figure_for+1): # go through the grid and follow the push every button under a lit one
            #print 'i =', i
            for j,k in enumerate(rows[i]):
                if rows[i-1][j] == '0':
                    rows[i-1][j] = '1'
                    if rows[i][j] == '0':
                        rows[i][j] = '1'
                    elif rows[i][j] == '1':
                        rows[i][j] = '0'
                    if j > 0:
                        if rows[i][j-1] == '1':
                            rows[i][j-1] = '0'
                        elif rows[i][j-1] == '0':
                            rows[i][j-1] = '1'
                    if j < figure_for-1:
                        if rows[i][j+1] == '1':
                            rows[i][j+1] = '0'
                        elif rows[i][j+1] == '0':
                            rows[i][j+1] = '1'
                    if i < figure_for:
                        if rows[i+1][j] == '0':
                            rows[i+1][j] = '1'
                        elif rows[i+1][j] == '1':
                            rows[i+1][j] = '0'
        # Get the last row, that becomes the solution
        try:
            #print start, ''.join(rows[figure_for])
            curs.execute("INSERT INTO final_%d VALUES (?,?)" % (figure_for), (start, ''.join(rows[figure_for])))
            count += 1
        except Exception, err:
            #print 'Error:', err
            errors += 1
        #print "%d entries, %d errors" % (count, errors)
        conn.commit()
        go += 1
    print figure_for, 'complete,', count, 'entries,', errors, 'duplicates'
    figure_for += 1
    '''
    5 complete, 8 entries, 23 duplicates
    6 complete, 63 entries, 0 duplicates
    7 complete, 127 entries, 0 duplicates
    8 complete, 255 entries, 0 duplicates
    9 complete, 2 entries, 509 duplicates
    10 complete, 1023 entries, 0 duplicates
    11 complete, 32 entries, 2015 duplicates
    12 complete, 4095 entries, 0 duplicates
    13 complete, 8191 entries, 0 duplicates
    14 complete, 1024 entries, 15359 duplicates
    15 complete, 32767 entries, 0 duplicates
    16 complete, 256 entries, 65279 duplicates
    17 complete, 32768 entries, 98303 duplicates
    18 complete, 262143 entries, 0 duplicates
    19 complete, 8 entries, 524279 duplicates
    20 complete, 1048575 entries, 0 duplicates
'''
