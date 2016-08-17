<!DOCTYPE html>
<html>
<head>
	<style>
		body {
			background-color: black;
			margin: 0;
		}
		canvas{
			margin: 0 auto;
			display: block;
		}
	</style>
	<script>
	"use strict";
	var docReady = false;
	/**
	 * Constants
	**/
	
	var WIDTH = window.innerWidth;
	var HEIGHT = window.innerHeight;
	var context, canvas;

	var tiles = [];
	var tileSelected;
	var entities = [];
	var screenXYOffset = {x: WIDTH / 2, y: HEIGHT/2};
	var TILE_IMAGES = [];
	var screenXYSpeed = {x: 0, y: 0};
	var KEY = {
		w: 87,
		a: 65,
		s: 83,
		d: 68,
	};

	var MENU = {
		x: 12,
		y: 12,
		width: 90,
		height: HEIGHT,
		buttons: [],
	};

	var tileImgSrc = [
	<?php
		$imageNames = scandir("map_tiles");
		
		//remove two elements (".", and "..").
		array_shift($imageNames);
		array_shift($imageNames);
		
		function addQuotes(&$item){
			$item = "\"".$item."\"";
		}
		array_walk($imageNames, "addQuotes");
		echo implode($imageNames, ",\n");

	?>
	];
	var entityImgSrc = [
	<?php
		$imageNames = scandir("map_entities");
		
		//remove two elements (".", and "..").
		array_shift($imageNames);
		array_shift($imageNames);
		
		array_walk($imageNames, "addQuotes");
		echo implode($imageNames, ",\n");

	?>
	];
	

	window.onresize = function(){
		WIDTH = window.innerWidth;
		HEIGHT = window.innerHeight;
		canvas.width = WIDTH;
		canvas.height = HEIGHT;
	}

	function retrieveContext(){
		canvas = document.body.appendChild(document.createElement("canvas"));
		canvas.width = WIDTH;
		canvas.height= HEIGHT;
		context = canvas.getContext("2d");
	}
	function drawThings(){
		//draw the tile pallet/general ui
		drawMenu();

		drawList(tiles);
		drawList(entities);

		
	}	
	function drawList(which){
		for(var i=0; i<which.length; i++){
			context.drawImage(which[i].i, which[i].x*32+screenXYOffset.x, which[i].y*32+screenXYOffset.y);

		}

	}


	function drawMenu(){
		context.fillStyle = "#00B";
		context.fillRect(MENU.x, MENU.y, MENU.width, MENU.height);	
		drawList(MENU.buttons);
		
		for(var i=0; i<MENU.buttons.length; i++){
			context.drawImage(MENU.buttons[i].i, MENU.buttons[i].x, MENU.buttons[i].y);
			if(MENU.buttons[i].i == tileSelected){
				context.fillStyle = "#0F0";
				context.rect(tileSelected, MENU.x + MENU.width/2 - tileSelected.width/2, MENU.y *2 + i*40, tileSelected.width, tileSelected.height);
			}
		}
	}
	
	function menuSelect(x, y){
		for(var i = 0,len=MENU.buttons.length; i<len; i++){
			if(MENU.buttons[i].x < x && MENU.buttons[i].x + MENU.buttons[i].width > x && MENU.buttons[i].y < y && MENU.buttons[i].y + MENU.buttons[i].width > y){
				tileSelected = TILE_IMAGES[i];
				break;
			}
		}
	}

	function addTile(x, y, tileImg){
		tiles.push({x: Math.floor((x-screenXYOffset.x)/32), y: Math.floor((y-screenXYOffset.y)/32), i:tileImg});
	}

	function clicked(x, y){
		if(menuClicked(x, y)){
			menuSelect(x, y);
		}
		else{
			addTile(x, y, tileSelected);
		}
	}

	function menuClicked(x, y){
		if(x > MENU.x && x < MENU.x + MENU.width){
			if(y > MENU.y && y < MENU.y + MENU.height){
				return true;
			}
		}
	}

	function clearDraw(){
		context.fillStyle = "#000000";
		context.fillRect(0,0, WIDTH, HEIGHT);
	}
	function loadThings(){
		for(var i=0; i<tileImgSrc.length; i++){
			var img = document.createElement("img");
			//set these before setting source - ensure they fire.
			
			img.onload = function(event){
				TILE_IMAGES.push(event.path[0]);
				newTileButton(event.path[0]);
			}
			img.src="map_tiles/"+tileImgSrc[i];

		}
	}
	
	function newTileButton(img){
		var x,y;
		var cols = Math.floor(MENU.width / (img.width+16));

		x = MENU.x;
		x += MENU.width/(cols+1)*((MENU.buttons.length%cols)+1);
		x -= img.width/2
		y = MENU.y + Math.floor(MENU.buttons.length/cols) * 40;
		MENU.buttons.push({x: x, y: y, i: img});
	}


	function ready(){
		loadThings();

		retrieveContext();
		canvas.onclick = function(e){
			clicked(e.clientX, e.clientY);
		}
		//start the recurring step function
		window.requestAnimFrame = 
			window.requestAnimationFrame||
			window.webkitRequestAnimationFrame||
        	window.mozRequestAnimationFrame;

  		if(window.requestAnimFrame){
  			(function stepHandler(){
  				window.requestAnimFrame(stepHandler);  				
  				step();
  			})();
  		}
  		else{
  			setInterval(step,1000/60);
  			console.debug("Browser does not support requestAnimationFrame");
  		}
		docReady = true;
	}

	function step(){
		moveScreen();
		clearDraw();
		drawThings();
	}

	function moveScreen(){
		screenXYOffset.x += screenXYSpeed.x;
		screenXYOffset.y += screenXYSpeed.y;
	}

	//which key was pressed
	function which(keyDown, event){
		if(docReady){
			if(!keyDown){
				if(event.keyCode == KEY.w || event.keyCode == KEY.s){
					screenXYSpeed.y = 0;
				} else if(event.keyCode == KEY.a || event.keyCode == KEY.d){
					screenXYSpeed.x = 0;
				}
			} else{
				if(event.keyCode == KEY.w){
					screenXYSpeed.y = 1;
				} else if(event.keyCode == KEY.a){
					screenXYSpeed.x = 1;
				} else if(event.keyCode == KEY.s){
					screenXYSpeed.y = -1;
				} else if(event.keyCode == KEY.d){
					screenXYSpeed.x = -1;
				}
			}
		}
	}
	</script>
</head>
<body onKeyDown="which(true,event)" onload="ready()" onKeyUp="which(false,event)">
</body>
</html>
