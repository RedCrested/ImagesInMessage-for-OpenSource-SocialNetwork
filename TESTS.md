# TEST SEQUENCE:
 
  | #Test | #Cache Disabled | #Cache Enabled |
  | :---: | :---: | :---: |
  | Take a list of files in photos folder (in my case, empty) | <b>[OK]</b> | <b>[OK]</b> | 
  | Send a image | <b>[OK]</b> |  <b>[OK]</b> | 
  | Send a text message | <b>[OK]</b> | <b>[OK]</b> | 
  | Send a image and text message | <b>[OK]</b> | <b>[OK]</b> | 
  | Pick a image an delete without sent | <b>[OK]</b> | <b>[OK]</b> | 
  | Without reload page, send another image | <b>[OK]</b> | <b>[OK]</b> | 
  | Pick a image and, without delete old image, pick and send another image. Old file must be removed | <b>[OK]</b> | <b>[OK]</b> | 
  | Pick a image and reload page. File must be deleted  | <b>[OK]</b> | <b>[OK]</b> | 
  | Delete some message with image with option "Remove for everyone" selected | <b>[OK]</b> | <b>[OK]</b> | 
  | Delete some message with image with option "Remove for you" selected. Image must remain in folder | <b>[OK]</b> | <b>[OK]</b> | 
  | Remove a user with folder images. Check if image folder are erased | <b>Not tested</b> | <b>Not tested</b> | 
  | Delete all tests. Folder must have inital files + file from message deleted with option "Remove for you" (in my test, one file) | <b>[OK]</b> | <b>[OK]</b> | 
  
 ## NOTES: 
 - Check file creation/removal on .../OSSN_DATA/MESSAGES/PHOTOS in every step:
 - I tested with cache disabled first
 - I run this test sequence in Chrome 99.0.4844.82 (oficial) 64 bits, Firefox 98.0.2 (64-bit) and Edgde 99.0.1150.46 (oficial) 64 bits
  
  