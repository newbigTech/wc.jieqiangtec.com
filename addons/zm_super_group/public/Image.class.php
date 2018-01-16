<?php
class Image{
    private $_image = '';
    private $_imageW = '';
    private $_imageH = '';
    private $_imageT = '';
    
    public function __construct($_image){
        $this->_image = $_image;
        list($this->_imageW,$this->_imageH,$this->_imageT) = getimagesize($_image);
    }
    
    public function getWidth(){
        return $this->_imageW;
    }
    
    public function getHeight(){
        return $this->_imageH;
    }
    
    public function getImageType(){
        return $this->_imageT; 
    }
    
    public function createNewImg($_size,$_newWidth,$_newHeight = ''){    
        if($this->_imageW > $this->_imageH){
           $_ratio = $this->_imageH / $this->_imageW; 
           $_newHeight = $_newWidth * $_ratio; 
        }else if($this->_imageW < $this->_imageH){
            $_newHeight = $this->_imageH * ($_newWidth / $this->_imageW);
        }else{
            $_newHeight = $_newWidth;
        }
        $_old = $this->openImg();
        $_newImage = imagecreatetruecolor($_newWidth,$_newHeight);  
        imagecopyresampled($_newImage,$_old,0,0,0,0,$_newWidth,$_newHeight,$this->_imageW,$this->_imageH);   
        $_tempArr = explode('.',$this->_image);
        $_endStr = end($_tempArr);
        $_filename = substr_replace($this->_image,'_'.$_size.'.jpg',-(strlen($_endStr)+1));
        imagejpeg($_newImage,$_filename,100); 
        imagedestroy($_newImage);
        imagedestroy($_old);
        $_return = array();
        $_return['url'] = $_filename;
        $_return['width'] = $_newWidth;
        $_return['height'] = $_newHeight;
        return $_return;  
    }  
    
    public function resetSize($_newWidth,$_newHeight){
        if($this->_imageW < 800 && $this->_imageH > 500){
            $_newWidth = $this->_imageW;
            $_newHeight = $this->_imageW / 1.6;
            $_dstX = 0;
            $_dstY = 0;
            $_srcX = 0;
            $_srcY = ($this->_imageH - 500) / 2;
            $_dstW = $_newWidth;
            $_dstH = $_newHeight;
            $_srcW = $_newWidth;
            $_srcH = $_newHeight;
        }
        $_newImg = imagecreatetruecolor($_newWidth,$_newHeight);
        $_old = $this->openImg();
        imagecopyresampled($_newImg,$_old,$_dstX,$_dstY,$_srcX,$_srcY,$_dstW,$_dstH,$_srcW,$_srcH);
        if($this->_imageT == 1){
            imagegif($_newImg,$this->_image);
        }
        if($this->_imageT == 2){
            imagejpeg($_newImg,$this->_image);
        }
        if($this->_imageT == 3){
            imagepng($_newImg,$this->_image);
        }
        imagedestroy($_newImg);
        imagedestroy($_old);
        return $this->_image;
    }
    
    private function openImg(){
        switch($this->_imageT){
            case 1:
                return imagecreatefromgif($this->_image);
                break;
            case 2:
                return imagecreatefromjpeg($this->_image);
                break;
            case 3:
                return imagecreatefrompng($this->_image);
                break;
            default:
                return '不支持此类型';
                break;
        }
    }
    
}