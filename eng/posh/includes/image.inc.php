<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
# ***************************************
# IMAGES management class
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

class img
{
	var $location;
	var $width;
	var $height;
	var $imgSrc;
	var $imgType;
	/*
		Ctor
		Inputs :
			$fileSrc (string) : image file location
	*/
	function img($fileSrc)
	{
		$this->location = $fileSrc;
		$size=getimagesize($this->location);
		$this->width=$size[0];
		$this->height=$size[1];
	}
	/*
		createGif : créer une image GIF
	*/
	function createGif()
	{
		$this->imgSrc=imagecreatefromgif($this->location);
		$this->imgType="gif";
	}
	/*
		createJpg : créer une image JPG
	*/
	function createJpg()
	{
		$this->imgSrc=imagecreatefromjpeg($this->location);
		$this->imgType="jpg";
	}
	/*
		createPng: créer une image PNG
	*/
	function createPng()
	{
		$this->imgSrc=imagecreatefrompng($this->location);
		$this->imgType="png";
	}
	/*
		resize : resize an image
		Inputs :
			$fileDest (string) : destination file location
			$newWidth (integer) : new width
			$newHeight (integer) : new height
			$srcdecx (integer) : 
			$srcdecy (integer) : 
	*/
	function resize($fileDest,$newWidth,$newHeight,$srcdecx,$srcdecy)
	{
		$imgDest=imagecreatetruecolor($newWidth,$newHeight);
		@imagecopyresized($imgDest,$this->imgSrc,0,0,$srcdecx,$srcdecy,$newWidth,$newHeight,$this->width,$this->height);
		if ($this->imgType=="gif" && function_exists("imagegif"))
		{
			if(!imagegif($imgDest,$fileDest)) $this->warning(1);
		} else {
			if(!imagejpeg($imgDest,$fileDest)) $this->warning(2);
		}
	}
	function warning($id)
	{
		echo "Image issue (error ".$id.")";
	}
}
?>