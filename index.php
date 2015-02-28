<!DOCTYPE html>
<?php if (isset($_POST['size']))
      {  
        $number = $_POST['size'];
      }
      else
      {
        $number = 5;
      }?>
<html>
	<head>
		<title>Lights On</title>
		<style>
			html
			{
				background-color:whitesmoke;
			}
			td
			{
				<?php if($number < 11): ?>
                width:50px;
				height:50px;
                <?php elseif ($number < 30): ?>
                width:25px;
				height:25px;
                <?php elseif ($number <= 50): ?>
                width:10px;
				height:10px;
                <?php else: ?>
                width:5px;
				height:5px;
                <?php endif;?>
				background-color:black;
				border:1px solid white;
			}
			table
			{

				margin:auto;
			}
			.lit
			{
				background-color:yellow;
			}
            .alit
			{
				background-color:yellow;
			}
			#score, #moves
			{
				color:red;

				margin:auto;
				width:140px;
			}
            input
            {
                position: relative;
                margin: 10px 0;
            }
			.center
			{
				margin: auto;
				width: 500px;
			}

		</style>
	</head>
	<body>
        <table>
            <tr id='solution'>
				<?php for ($i = 0; $i <$number; $i++): ?> 
                <td></td>
				<?php endfor; ?>
			</tr>
        </table>
		<div id="moves">Move Number: 0</div>
		<table id="building">
            <?php for ($i = 0; $i <$number; $i++): ?>
			<tr id='<?php echo $i; ?>'>
                <?php for ($j = 0; $j <$number; $j++): ?> 
                <td></td>
				<?php endfor; ?>
			</tr>
			<?php endfor; ?>
		</table>
		<div id="score">0/<?php echo $number*$number; ?> Room(s) Lit</div>
		<div class='center'>
			<pre>A solution generator exists for this puzzle.
If you want to use it, simply click the Find Answer button.
Then just flip every switch under an unlit switch.</pre>
			<input type='button' onclick='solution()' value='find answer'/>
		</div>
        <form class='center' method='POST' action=''>
			Change Grid size
			<select id='gridSize' name='size'>
				  <?php for($i=5; $i<21; $i++): ?>
				  <option value='<?php echo $i;?>' <?php if($i==$number){echo 'Selected';}?>>
						<?php echo $i; ?> Rows
				  </option>
				  <?php endfor; ?>
			</select>
			<input type='submit' onclick='changeGrid' value='GO!'/>
		</form>
		<script type="text/javascript">
			function changeGrid(){
			    var size = document.getElementById('gridSize').selectedIndex + 5;
				
			}
			//Defines starting variables.
			var building = document.getElementById("building");//Main table used.
			var buildingLevel = building.rows.length;//Rows in the table.
			var buildingFloors = building.rows[0].cells.length;//Columns in the table.
			var roomsLit = document.getElementById("score");//Score DIV.
			var flipped = document.getElementById("moves");//Moves DIV.
			var switchesFlipped = -<?php echo $number*10; ?>;//Beginning amount of moves.  Set to -50 due to seeding of moves for starting the game.
			var go;
			//Namespace for the game.
			var lightsOn = lightsOn || {
				
				//Initial setup of the game.
				setUp: function()
				{
					//Sets an onclick event for each part of the table.  Compatible with IE8.
					for(var i = 0; i < buildingLevel; i++)
					{
						for(var j = 0; j < buildingFloors; j++)
						{
							var lightSwitch = building.rows[i].cells[j];
							if(lightSwitch.attachEvent)
							{
								lightSwitch.setAttribute("onclick", "lightsOn.checkSwitch(this)");
							}
							else
							{
								lightSwitch.addEventListener("click", function(){lightsOn.checkSwitch(this);}, false);
							}
						}
					}
					//this.randomize();
                    go = setInterval('lightsOn.randomize()', 10);				
				},
				
				//Resets the game.
				resetGame: function()
				{
					//Resets the moves.
					switchesFlipped = -<?php echo $number*10; ?>;
					
					//Runs the initial setup again.
					//this.randomize();
                    var datas = document.getElementById('solution').getElementsByTagName('td');
                    for (var j=0; j<<?php echo $number; ?>; j++)
                    {
                        datas[j].className = '';
                    }
                    go = setInterval('lightsOn.randomize()', 15);
				},
                
				randomize: function()
                {
						var x = Math.floor(Math.random() * <?php echo $number; ?>);
						var y = Math.floor(Math.random() * <?php echo $number; ?>);
						
						this.burnElectricity(x, y);
                        if (switchesFlipped == 0)
                        {
                            clearInterval(go);
                        }
                },
				//Gets the data from the clicked cell and turns it in to x and y coordinates.
				checkSwitch: function(data)
				{
					var roomToChange = data;
					var roomLevel = data.parentNode;
					var x = roomToChange.cellIndex;
					var y = roomLevel.rowIndex;
					
					//Sends the x and y coordinates to start burning some electricity!
					this.burnElectricity(x, y);
				},
				
				//Based on the coordinates sent, lights get switched.
				burnElectricity: function(x, y)
				{
					if((x >= 0) && (x < <?php echo $number; ?>) && (y >= 0) && (y < <?php echo $number; ?>))
					{
						switch(x)
						{
							case 0:
								this.flipSwitch(x, y);
								this.flipSwitch(x + 1, y);
								break;
							case <?php echo $number-1; ?>:
								this.flipSwitch(x, y);
								this.flipSwitch(x - 1, y);
								break;
							default:
								this.flipSwitch(x, y);
								this.flipSwitch(x + 1, y);
								this.flipSwitch(x - 1, y);
								break;
						}
						
						switch(y)
						{
							case 0:
								this.flipSwitch(x, y + 1);
								break;
							case <?php echo $number-1; ?>:
								this.flipSwitch(x, y - 1);
								break;
							default:
								this.flipSwitch(x, y + 1);
								this.flipSwitch(x, y - 1);
								break;
						}
					}
					
					//Increments the moves.
					switchesFlipped++;
					
					//Checks to see if you're a winner!
					this.win();
				},
				
				//Adds or removes the "lit" class based on the x and y coordinates.
				flipSwitch: function(x, y)
				{
					var room = building.rows[y].cells[x];
					if(room.hasAttribute("class"))
					{
						room.removeAttribute("class");
					}
					else
					{
						room.setAttribute("class", "lit");
					}
				},
				
				//Checks to see if you win.
				win: function()
				{
					//Counts how many rooms are lit.
					var litRooms = document.querySelectorAll(".lit").length;
					
					//Sets up the scoreboard.
					roomsLit.innerHTML = litRooms + "/<?php echo $number*$number; ?> Room(s) Lit";
					
					//Sets up the move count.
					flipped.innerHTML = "Move Number: " + switchesFlipped;
					
					//If this was a winning move, this will tell you!
					if(litRooms == <?php echo $number*$number; ?>)
					{
						alert("Congrats!  You win!  You took " + switchesFlipped + " moves to finish.");
						
						//Resets the game.
						this.resetGame();
					}
				}
			}
			
			//Starts the game.
			lightsOn.setUp();
            
            function solution()
            {
                var post, grid = [];
                for (var i=0; i<<?php echo $number; ?>; i++)
                {
                    var datas = document.getElementById(i).getElementsByTagName('td');
                    var row = [];
                    for (var j=0; j<<?php echo $number; ?>; j++)
                    {
                        if (datas[j].className != 'lit')
                        {
                            row.push(1);
                        }
                        else
                        {
                            row.push(0);
                        }
                    }
                    grid.push(row.join(''));
                }
				post = 'grid=' + JSON.stringify(grid);
                var url = '/pyServe/lights/solve';
                var expireCheck = new XMLHttpRequest();
                    expireCheck.open("POST", url, true);
                    expireCheck.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    expireCheck.onreadystatechange=function() 
                    {
                        if (expireCheck.readyState==4) 
                        {
                            var datas = document.getElementById('solution').getElementsByTagName('td');
                            for (var j=0; j<<?php echo $number; ?>; j++)
                            {
                                if (expireCheck.responseText[j] == '0')
                                {
                                    datas[j].className = '';
                                }
                                else
                                {
                                    datas[j].className = 'alit';
                                }
                            }
                        }
                    }
                    expireCheck.send(post);
            }
            
            function cheat()
            {
                var rooms = building.getElementsByTagName('td');
                for (var i=rooms.length-1; i>=0; i--)
                {
                    rooms[i].className = 'lit';
                }
            }
		</script>
	</body>
</html>