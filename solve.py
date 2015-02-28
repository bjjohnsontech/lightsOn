#! /usr/bin/python

import sqlite3
import json

conn = sqlite3.connect('/var/www/html/codeprojects/lights/lights.db')
#print sys.argv[1]
curs = conn.cursor()

def solution(grid):
    # json to list of strings
    grid = json.loads(grid)
    # list of strings to list of lists
    grid = [list(x) for x in grid]
    figure_for = len(grid) - 1
    for i in range(1, figure_for + 1): # go through the grid and push every button below an unlit one
        #print 'i =', i
        for j,k in enumerate(grid[i]):
            if grid[i-1][j] == '0': # if above is 'unlit' hit switch
                grid[i-1][j] = '1'
                if grid[i][j] == '1': # toggle switch
                    grid[i][j] = '0'
                else:
                    grid[i][j] = '1'
                if j > 0: # if not in first column
                    if grid[i][j-1] == '0': # toggle switch to the left
                        grid[i][j-1] = '1'
                    else:
                        grid[i][j-1] = '0'
                if j < figure_for: # if not in last column
                    if grid[i][j+1] == '0': # toggle switch to the right
                        grid[i][j+1] = '1'
                    else:
                        grid[i][j+1] = '0'
                if i < figure_for: # if not last row 
                    if grid[i+1][j] == '1': # toggle below
                        grid[i+1][j] = '0'
                    else:
                        grid[i+1][j] = '1'
        
        # Get the last row, reverse it look up the slution in the db. return solution
    curs.execute("SELECT start FROM final_%d WHERE finish='%s'" % (figure_for + 1, ''.join(grid[-1])))
    return curs.fetchone()[0]