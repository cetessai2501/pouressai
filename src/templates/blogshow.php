<?php $this->layout('layout', ['title' => $post->name]) ?>
<?php $proce = array() ;?>
<?php if(!empty($commentas)): ?>
<?php foreach ($commentas as $comment) : ?>

<?php array_push($proce, intval($comment['pid']));?>
<?php 

$price[intval($comment['pid'])] = $comment ;

?>
<?php endforeach; ?>
<?php

$count = array_count_values($proce);
$counter = $count[intval($post->id)];

?>
<?php endif; ?> 
<?php 
$toka = $this->csrf_input();

$firstu =substr( $toka ,41);
$tok = substr($firstu, 0, 32);


function format_comments($commentas, $router, $user) {
		$html = array();
		$root_id = 0;
		foreach ($commentas as $comment)
                        
			$children[intval($comment['parent_id'])][] = $comment;
                        

		// loop will be false if the root has no children (i.e., an empty comment!)
		$loop = !empty($children[$root_id]);

		// initializing $parent as the root
		$parent = $root_id;
		$parent_stack = array();

		// HTML wrapper for the menu (open)
		$html[] = '<div id="one" class="container"><ul class="comment">';

		while ($loop && ( ( $option = each($children[$parent]) ) || ( $parent > $root_id ) )) {

			if ($option === false) {
				$parent = array_pop($parent_stack);

				// HTML for comment item containing childrens (close)
				$html[] = str_repeat("\t", ( count($parent_stack) + 1 ) * 2) . '</ul>';
				$html[] = str_repeat("\t", ( count($parent_stack) + 1 ) * 2 - 1) . '</li>';
			} elseif (!empty($children[$option['value']['id']])) {

//var_dump(intval($option['value']->id));
				$tab = str_repeat("\t", ( count($parent_stack) + 1 ) * 2 - 1);
				$keep_track_depth = count($parent_stack);
				if ($keep_track_depth <= 3) {
		$reply_link = '%1$s%1$s<a href="#" id="%2$s" class="repli" style="text-decoration:none;" data-id="%2$s" data-email="' . $option["value"]["email"] .   '">reply</a><br/>%1$s';
                                        $del_link = '%1$s%1$s<a href="#" id="%2$s">delete</a><br/>%1$s';
                                        if($user !== null){
                                        $del = $router->relativeUrlFor("delete.comments", ["id" => $option["value"]["id"] ]);
                                        }else{
                                         $del =  '';
                                        } 
                                         
				} else {
					$reply_link = '';
                                        $del_link = ''; 
                                        $del =  '';
                                              
				}
				//$reply_link = '%1$s%1$s<a href="#" class="reply_button" id="%2$s">reply</a><br/>';
				// HTML for comment item containing childrens (open)
                                $html[] = sprintf(
	'%1$s<li id="comment-%2$s" data-depth-level="' . $keep_track_depth . '">' .'<span class="comment_date">%4$s</span>' .
	'%1$s%1$s<div style="margin-top:4px;">%3$s</div>' . '<a href="' . $del . '" style="text-decoration:none;">delete</a> ' .
						$reply_link . '</li>', $tab, // %1$s = tabulation
						$option['value']['id'], //%2$s id
						$option['value']['content'], // %3$s = comment
						$option['value']['comment_time'] // %4$s = comment created_date
				); 

				//$check_status = "";
				$html[] = $tab . "\t" . '<ul class="comment">';
                                array_push($parent_stack, $option['value']['parent_id']);
				$parent = $option['value']['id']; 

				//array_push($parent_stack, $option['value']['parent_id']);
				//$parent = $option['value']['comment_id'];
			} else {
				$keep_track_depth = count($parent_stack);
				if ($keep_track_depth <= 3) {
$reply_link = '%1$s%1$s<a href="#"  class="repli" style="text-decoration:none;" data-id="%2$s" data-email="' . $option["value"]["email"] .   '"   >reply</a><br/>%1$s';
                                        //$del = $router->relativeUrlFor("delete.comments", ["id" => $option["value"]["id"] ]);
                                        $email2 = $option["value"]["email"];
                                        if($user !== null){
                                        $del = $router->relativeUrlFor("delete.comments", ["id" => $option["value"]["id"] ]);
                                        }else{
                                         $del =  '';
                                        }  
                                         
 
				} else {
					$reply_link = '';
                                        $del =  '';
                                        $email2 = $option["value"]["email"];
                                               
				}

				//$reply_link = '%1$s%1$s<a href="#" class="reply_button" id="%2$s">reply</a><br/>%1$s</li>';
				// HTML for comment item with no children (aka "leaf")
                               	$html[] = sprintf(
						'%1$s<li id="comment-%2$s" data-depth-level="' . $keep_track_depth . '">' .
			                        '<span class="comment_date">%4$s</span>' .  
						'%1$s%1$s<div style="margin-top:4px;">%3$s</div>'.  '<a href="' . $del . '" style="text-decoration:none;">delete</a> ' .
						$reply_link . '</li>', str_repeat("\t", ( count($parent_stack) + 1 ) * 2 - 1), // %1$s = tabulation
                                                
						$option['value']['id'], //%2$s id
                                                 
						$option['value']['content'], // %3$s = comment
						$option['value']['comment_time'] // %4$s = comment created_date
                                                
				); 
				
			}
		}


		// HTML wrapper for the comment (close)
		$html[] = '</ul></div>';
		return implode("\r\n", $html);
	}







function randHash($len=32)
{
	return substr(md5(openssl_random_pseudo_bytes(20)),-$len);
}

function gravatar($email = '', $rating = 'pg') {
    //$default = "/home/sophie25/palipum/public/uploads/posts/defava.png"; // Set a Default Avatar
    //$file = file_get_contents($default); 
    //$imageData = base64_encode($file);
    $email2 = $email; 
    //$src = 'data: '.mime_content_type($default).';base64,'.$imageData;
    $email = md5(strtolower(trim($email)));
    //$string = randHash(); 
    //$grav = "https://www.gravatar.com/avatar/$string?s=32&d=identicon&r=PG";
    $gravatar = "http://www.gravatar.com/avatar/$email?d=404";  
    //$gravurl = "http://www.gravatar.com/avatar/$email?d=$default&s=60&r=$rating";
    $src2 = "http://www.gravatar.com/avatar";
$headers = get_headers($gravatar,1);
if (strpos($headers[0],'200')) echo "<li><img src='$gravatar' width='40' height='40' title='$email2' alt='Avatar'></li>"; // OK
else if (strpos($headers[0],'404')) echo '<li><img src="'.$src2.'" width="40" height="40" border="0" alt="Avatar" title="Gravatar"><li>'; // Not Found
}


function mygravatar() {
$default = "/home/sophie25/palipum/public/svg/reply.png";
$file = file_get_contents($default); 
$imageData = base64_encode($file);
$src = 'data: '.mime_content_type($default).';base64,'.$imageData;
echo '<img src="'.$src.'" width="20" height="20"  alt="Image">';

}
function mydelete() {
$default = "/home/sophie25/palipum/public/svg/comments.png";
$file = file_get_contents($default); 
$imageData = base64_encode($file);
$src = 'data: '.mime_content_type($default).';base64,'.$imageData;
echo '<img style="top:-20px;left:10px;position:relative;" src="'.$src.'" width="60" height="60"  alt="Image">';

}
?>

<div id="two" class="container" style="margin-top:40px;">
<?php if(isset($_SESSION['flash']['success'])): ?>
<div class="alert alert-success"><?php echo $_SESSION['flash']['success'] ;?> </div>
<?php unset($_SESSION['flash']['success']); ?>
<?php endif; ?>
<?php if(isset($_SESSION['flash']['error'])): ?>


<?php foreach ($_SESSION['flash']['details'] as $detail) : ?>
<div class="alert alert-danger"><?php echo $detail ;?>
 </div>
<?php endforeach; ?>
<?php unset($_SESSION['flash']['error']); ?>
<?php unset($_SESSION['flash']['details']); ?>
<?php endif; ?>

<?php if($post->id === 29): ?>
<img src="<?= $post->getImageUrl();?>" alt="<?= $post->getImageUrl() ;?>">
<?php else: ?> 
<img src="<?= $post->getThumb();?>" alt="<?= $post->getImageUrl() ;?>">
<?php endif; ?> 
 <h1><?= $post->name ;?></h1><h2><?= $post->id ;?></h2>
<p><strong> Categorie :  <?php echo $post->categoryName; ?></strong></p>

<p class="text-muted"><?= $post->getTime();?></p>

<p><?= $post->content ;?></p>

<div class="col-md-4">
<?php foreach ($post->getTags() as $to) : ?>


<p ><a href="<?php echo $router->relativeUrlFor('taggi', ['slug' => $post->slug ], ['tag' => $to]);?>" class="badge badge-danger"><h2><?php echo $to; ?></h2></a></p>



           
<?php endforeach; ?>

</div>
</div><br><!-- f row --> 
<div class="container">
<?php 


$ci = 0;
$useri = isset($user->id) ? $user->id : 0; 
$usera = isset($user->id) ? $user->id : 0; 

$session_value =(isset($_SESSION['csrf.tokens']))?$_SESSION['csrf.tokens']:'';
$toko =  $this->csrf_value();

$counter = isset($counter) ? $counter : 0;
if(!empty($commentas)){
echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" style="margin-left:60px;z-index: -2;fill:#0074D9;" width="60" height="60"><rect x="0" fill="none" width="16" height="16"/><g><path d="M12 4H4c-1.1 0-2 .9-2 2v8l2.4-2.4c.4-.4.9-.6 1.4-.6H12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"/></g></svg>';
echo '<span id="num" class="number">' . $counter . '</span>';

echo '<span class="mytext">Commentaires</span>';
//echo format_comments($commentas, $router, $user);
}else{
echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" style="margin-left:60px;z-index: -2;fill:#0074D9;" width="60" height="60"><rect x="0" fill="none" width="16" height="16"/><g><path d="M12 4H4c-1.1 0-2 .9-2 2v8l2.4-2.4c.4-.4.9-.6 1.4-.6H12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"/></g></svg>';
echo '<span class="number">' . 0 . '</span>';
echo '<span class="mytext">Commentaires</span>';
}

 
?>
</div>
<div id=app><div class="ui container"><comments model=/blog/ slug='<?= $post->slug ;?>' csrf='<?= $tok ;?>' :user='<?= $usera ;?>'></comments></div></div><script type=text/javascript src=/static/js/manifest.2ae2e69a05c33dfc65f8.js></script><script type=text/javascript src=/static/js/vendor.16762b013fc9b2dc6d9e.js></script><script type=text/javascript src=/static/js/app.45b8f107f6037a25e7a4.js></script>

<script>


// L'observation peut être arrêtée par la suite


// Créé une instance de l'observateur lié à la fonction de callback


// Commence à observer le noeud cible pour les mutations précédemment configurées


// L'observation peut être arrêtée par la suite


jQuery( document ).ready( function($) {
var phpVar = "<?php echo $counter ;?>";
var user = "<?php echo $useri ;?>";
var toki = "<?php echo $toko ;?>";
var useri = parseInt(user);
var tokan = '<?php echo $this->csrf_input();?>';

var firstu = tokan.substring(41);

//data-v-133ed8df


var token = firstu.substring(0, 32);
console.log( token);
var tojo = '"'+ token +'"';

let datos = [{"id":749,"pseudo":"bbbbbbbbbbbbbbbbbbb","post_id":9,"content":"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv","created_at":"2019-06-30 20:03:57","user_id":null,"email":"oppppp@pomm.fr","parent_id":0,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 20:03:57"},{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-07-02 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-07-02 15:47:26"},{"id":638,"pseudo":"mmmmmmmmmmmmmmmmmm","post_id":9,"content":"aaaaaaaaaaaaaaaaaaaaaaaaaaa","created_at":"2019-06-30 15:47:09","user_id":null,"email":"iguane25@poli.net","parent_id":737,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:09","children":[{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-06-30 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:26"}]},{"id":737,"pseudo":"bbbbbbbbbbbbbbbbbb","post_id":9,"content":"ccccccccccccccccccccccccccccccccccccccc","created_at":"2019-06-30 15:46:35","user_id":null,"email":"iguane25@laposte.net","parent_id":0,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:46:35","children":[{"id":738,"pseudo":"mmmmmmmmmmmmmmmmmm","post_id":9,"content":"aaaaaaaaaaaaaaaaaaaaaaaaaaa","created_at":"2019-06-30 15:47:09","user_id":null,"email":"iguane25@poli.net","parent_id":737,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:09","children":[{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-06-30 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:26"}]}]}];
let obj = {};

let noucoms = datos.sort( function ( a, b ) { return b.id - a.id; } );
let a = {};
noucoms.forEach(comment=> {

obj[comment.id] =  comment



});





Date.prototype.getWeek = function (dowOffset) {
/*getWeek() was developed by Nick Baicoianu at MeanFreePath: http://www.meanfreepath.com */

    dowOffset = typeof(dowOffset) == 'int' ? dowOffset : 0; //default dowOffset to zero
    var newYear = new Date(this.getFullYear(),0,1);
    var day = newYear.getDay() - dowOffset; //the day of week the year begins on
    day = (day >= 0 ? day : day + 7);
    var daynum = Math.floor((this.getTime() - newYear.getTime() - 
    (this.getTimezoneOffset()-newYear.getTimezoneOffset())*60000)/86400000) + 1;
    var weeknum;
    //if the year starts before the middle of a week
    if(day < 4) {
        weeknum = Math.floor((daynum+day-1)/7) + 1;
        if(weeknum > 52) {
            nYear = new Date(this.getFullYear() + 1,0,1);
            nday = nYear.getDay() - dowOffset;
            nday = nday >= 0 ? nday : nday + 7;
            /*if the next year starts before the middle of
              the week, it is week #1 of that year*/
            weeknum = nday < 4 ? 1 : 53;
        }
    }
    else {
        weeknum = Math.floor((daynum+day-1)/7);
    }
    return weeknum;
};
///console.log(Object.keys(obj).sort((a,b) => b - a).reduce((result, key) => { result[key] = obj[key]; return result;} , {}));



//console.log(ajouterNote(eleve, 10));
//console.log(eleve);

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}



document.cookie ='XSRF-TOKEN='+ token  + '; expires=Session; path=/';
var x = document.cookie;



///Set-Cookie: Csrf-token=i8XNjC4b8KVok4uw5RftR38Wgp2BFwql; expires=Thu, 23-Jul-2015 10:25:33 GMT; Max-Age=31449600; Path=/
//document.cookie = "username=John Doe"; 

//document.cookie = "ppkcookie1=testcookie; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";


















});












  

</script>
 
