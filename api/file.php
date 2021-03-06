<?php 
    
/**
 * A protected API call to retrieve the current site
 * @uri /image/list/all
 */
class ImageListAllResource extends Tonic\Resource {

    /**
     * @method GET
     */
    function get() {
        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized
            
            $arr = array();
            
            $site = Site::GetBySiteId($authUser->SiteId);

            $directory = '../sites/'.$site['FriendlyId'].'/files/';
            
            //get all image files with a .html ext
            $files = glob($directory . "*.*");

            $arr = array();
            
            $image_exts = array('gif', 'png', 'jpg');
            
            //print each file name
            foreach($files as $file){
                $f_arr = explode("/",$file);
                $count = count($f_arr);
                $filename = $f_arr[$count-1];
                
                // get extension
                $parts = explode(".", $filename); 
            	$ext = end($parts); // get extension
        		$ext = strtolower($ext); // convert to lowercase
                
                // is image
                $is_image = in_array($ext, $image_exts);
                
                // is thumb
                $is_thumb = false;
                
                if(substr($filename, 0, 2)=='t-'){
                    $is_thumb = true;
                }
                
                // get mimetype
                $mime_type = mime_content_type($directory.$filename);
                
                if($is_thumb==false && $is_image==true){
                    
                    list($width, $height, $type, $attr) = getimagesize($directory.$filename);
                    
                    $file = array(
                        'filename' => $filename,
                        'fullUrl' => 'sites/'.$site['FriendlyId'].'/files/'.$filename,
                        'thumbUrl' => 'sites/'.$site['FriendlyId'].'/files/t-'.$filename,
                        'extension' => $ext,
                        'mimetype' => $mime_type,
                        'isImage' => $is_image,
                        'width' => $width,
                        'height' => $height
                    );
                    
                    array_push($arr, $file); 
                }    

            }
            
            // return a json response
            $response = new Tonic\Response(Tonic\Response::OK);
            $response->contentType = 'applicaton/json';
            $response->body = json_encode($arr);

            return $response;
        }
        else{
            // return an unauthorized exception (401)
            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }
    
}

/**
 * A protected API call to retrieve the current site
 * @uri /file/post
 */
class FilePostResource extends Tonic\Resource {

    /**
     * @method POST
     */
    function get() {
        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized
            
            parse_str($this->request->data, $request); // parse request
            
            $arr = array();
            
            $site = Site::GetBySiteId($authUser->SiteId);
            
            // Get uploaded file info
        	$filename = $_FILES['file']['name'];  
    		$file = $_FILES['file']['tmp_name'];
    		$contentType = $_FILES['file']['type'];
    		$size = intval($_FILES['file']['size']/1024);
    		
    		$parts = explode(".", $filename); 
    		$ext = end($parts); // get extension
    		$ext = strtolower($ext); // convert to lowercase
            
            $thumbnail = 't-'.$filename;
            $directory = '../sites/'.$site['FriendlyId'].'/files/';
            
            // save image
            if($ext=='png' || $ext=='jpg' || $ext=='gif'){ // upload image
            
    			$size=Image::SaveImageWithThumb($directory, $filename, $file);
    			
    			list($width, $height, $type, $attr) = getimagesize($directory.$filename); // get width and height
                
                $arr = array(
                        'filename' => $filename,
                        'fullUrl' => 'sites/'.$site['FriendlyId'].'/files/'.$filename,
                        'thumbUrl' => 'sites/'.$site['FriendlyId'].'/files/t-'.$filename,
                        'extension' => $ext,
                        'mimetype' => $contentType,
                        'isImage' => true,
                        'width' => $width,
                        'height' => $height
                    );
                    
    		}
    		else if($ext=='ico' || $ext=='css' || $ext=='js' || $ext=='pdf' || $ext=='doc' || $ext=='docx' || $ext=='zip'){ // upload file

    			// upload file
    			Utilities::SaveFile($directory, $filename, $file);
                
                $arr = array(
                    'filename' => $filename,
                    'fullUrl' => 'sites/'.$site['FriendlyId'].'/files/'.$filename,
                    'thumbUrl' => 'sites/'.$site['FriendlyId'].'/files/t-'.$filename,
                    'extension' => $ext,
                    'mimetype' => $mime_type,
                    'isImage' => false,
                    'width' => -1,
                    'height' => -1
                );
    		}
    		else{
                return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
    		}
            
            // return a json response
            $response = new Tonic\Response(Tonic\Response::OK);
            $response->contentType = 'applicaton/json';
            $response->body = json_encode($arr);

            return $response;
        }
        else{
            // return an unauthorized exception (401)
            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }
    
}

/**
 * A protected API call to retrieve the current site
 * @uri /file/list/all
 */
class FileListAllResource extends Tonic\Resource {

    /**
     * @method GET
     */
    function get() {
        // get an authuser
        $authUser = new AuthUser();

        if(isset($authUser->UserUniqId)){ // check if authorized
            
            $arr = array();
            
            $site = Site::GetBySiteId($authUser->SiteId);

            $directory = '../sites/'.$site['FriendlyId'].'/files/';
            
            //get all image files with a .html ext
            $files = glob($directory . "*.*");

            $arr = array();
            
            $image_exts = array('gif', 'png', 'jpg');
            
            //print each file name
            foreach($files as $file){
                $f_arr = explode("/",$file);
                $count = count($f_arr);
                $filename = $f_arr[$count-1];
                
                // get extension
                $parts = explode(".", $filename); 
                $ext = end($parts); // get extension
        		$ext = strtolower($ext); // convert to lowercase
                
                // is image
                $is_image = in_array($ext, $image_exts);
                
                // is thumb
                $is_thumb = false;
                
                if(substr($filename, 0, 2)=='t-'){
                    $is_thumb = true;
                }
                
                // get mimetype
                $mime_type = mime_content_type($directory.$filename);
                
                if($is_thumb==false && $is_image==true){
                    
                    list($width, $height, $type, $attr) = getimagesize($directory.$filename);
                    
                    $file = array(
                        'filename' => $filename,
                        'fullUrl' => 'sites/'.$site['FriendlyId'].'/files/'.$filename,
                        'thumbUrl' => 'sites/'.$site['FriendlyId'].'/files/t-'.$filename,
                        'extension' => $ext,
                        'mimetype' => $mime_type,
                        'isImage' => $is_image,
                        'width' => $width,
                        'height' => $height
                    );
                    
                    array_push($arr, $file); 
                }
                else if($is_thumb==false){
                    $file = array(
                        'filename' => $filename,
                        'fullUrl' => 'sites/'.$site['FriendlyId'].'/files/'.$filename,
                        'thumbUrl' => 'n/a',
                        'extension' => $ext,
                        'mimetype' => $mime_type,
                        'isImage' => $is_image,
                        'width' => -1,
                        'height' => -1
                    );
                    
                    array_push($arr, $file); 
                }

            }
            
            // return a json response
            $response = new Tonic\Response(Tonic\Response::OK);
            $response->contentType = 'applicaton/json';
            $response->body = json_encode($arr);

            return $response;
        }
        else{
            // return an unauthorized exception (401)
            return new Tonic\Response(Tonic\Response::UNAUTHORIZED);
        }
    }
    
}


?>