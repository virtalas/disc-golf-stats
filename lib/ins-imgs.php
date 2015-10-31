<?php

  // Modified from: https://github.com/mikelothar/show-all-images-in-a-folder-with-php

  class ImageDisplayer {
    public static function display($path, $year) {
      $echo = "";

      # Path to image folder
      $imageFolder = $path. "/". $year. "/";

      # Show only these file types from the image folder
      $imageTypes = '{*.jpg,*.JPG,*.jpeg,*.JPEG,*.png,*.PNG,*.gif,*.GIF}';

      # Set to true if you prefer sorting images by name
      # If set to false, images will be sorted by date
      $sortByImageName = true;

      # Set to false if you want the oldest images to appear first
      # This is only used if images are sorted by date (see above)
      $newestImagesFirst = true;

      # The rest of the code is technical

      # Add images to array
      $images = glob($imageFolder . $imageTypes, GLOB_BRACE);

      # Sort images
      if ($sortByImageName) {
          $sortedImages = $images;
          natsort($sortedImages);
      } else {
          # Sort the images based on its 'last modified' time stamp
          $sortedImages = array();
          $count = count($images);
          for ($i = 0; $i < $count; $i++) {
              $sortedImages[date('YmdHis', filemtime($images[$i])) . $i] = $images[$i];
          }
          # Sort images in array
          if ($newestImagesFirst) {
              krsort($sortedImages);
          } else {
              ksort($sortedImages);
          }
      }

      $sortedImages = array_reverse($sortedImages);

      # Generate the HTML output
      // writeHtml('<ul class="ins-imgs">');
      foreach ($sortedImages as $image) {

          # Get the name of the image, stripped from image folder path and file type extension
          $name = substr($image, strlen($imageFolder), strpos($image, '.') - strlen($imageFolder));

          # Get the 'last modified' time stamp, make it human readable
          $lastModified = '(last modified: ' . date('F d Y H:i:s', filemtime($image)) . ')';

          # Begin adding
          // writeHtml('<li class="ins-imgs-li">');
          // writeHtml('<div class="ins-imgs-label">' . $name . ' ' . '</div>');
          // writeHtml('<div class="ins-imgs-img"><a name="' . $image . '" href="#' . $image . '">');
          $echo .= "<br><b>$name</b>";

          $image = substr($image, 58, strlen($image) - 58);
          $to_be = '<br><img src="' . $image . '" alt="' . $name . '" title="' . $name . '" style="max-width:800px"><br>';
          $to_be_written = str_replace("/Applications/XAMPP/htdocs", "", $to_be);

          $echo .= $to_be_written;
          // writeHtml('</a></div>');

          // writeHtml('</li>');
      }
      // writeHtml('</ul>');

      // writeHtml('<link rel="stylesheet" type="text/css" href="ins-imgs.css">');
      // $echo .= '<link rel="stylesheet" type="text/css" href="ins-imgs.css">';

      return $echo;
    }
  }
