<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Filesystem Functions');

$page_str = <<<EOPAGESTR5

<p>Most of these functions will be quite familiar to Unix users, as they closely replicate
common system commands.</p>

<p>Caution: Many of the functions in this section are dangerous. Because they duplicate
functions that can and should be performed from the local system, they can be a cracker's
bonanza without providing much value to legitimate users. Strongly consider disabling them
using PHP's disable_functions directive (as discussed in the preceding section on file
writing)!</p>

<p>The one piece of good news is that some of these functions will only work if the PHP
process is running as the superuser. Because this is not the default case in the Web browser,
presumably these functions are intended to be used by the scripting version of PHP, and only
trusted users who know what they're doing are even in a position to shoot themselves in the
foot this way. Of course, if you are foolish enough to run your Web server as root, you are
doubly screwed.</p>

<p>The most common and safest functions are listed first in the following sections; the less
common and less safe are in Table 23-1.</p>

<h2>feof</h2>

<p>The feof function tests for end-of-file on a file pointer and takes a filename as argument.
It's used mostly in a while loop to perform the same function on each line in a file:</p>

<pre>
while (!feof(&#36;fd)) {
  &#36;line = fgets(&#36;fd, 4096);
  echo &#36;line;
}
</pre>

<h2>file_exists</h2>

<p>The file_exists function is a simple function you will use again and again if you use
filesystem functions at all. It simply checks the local filesystem for a file of the
specified name.</p>

<pre>
if (!file_exists("testfile.php")) {
  &#36;fd = fopen("testfile.php", "w+");
}
</pre>

<p>The function returns true if the file exists, false if not found. The results of this
test are stored in a cache, which may be cleared by use of the function clearstatcache().</p>

<h2>filesize</h2>

<p>Another simple but useful function is filesize, which returns and caches the size of a
file in bytes. We use it in all the fread() examples earlier in this chapter. Never pass in a
filesize as an integer if you can do it by using filesize() instead.</p>

<br />

<table class="events" width="678">
  <caption>Table 23-1</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>basename (filepath, [suffix])</td>
    <td>Returns the filename portion of a stated path.</td>
  </tr>
  <tr>
    <td>chgrp(file, group)</td>
    <td>Changes file to any group to which the PHP process belongs. Inoperative on Windows
    systems.</td>
  </tr>
  <tr>
    <td>chmod(file, mode)</td>
    <td>Changes to the stated octal mode. Inoperative on Windows systems.</td>
  </tr>
  <tr>
    <td>chown(file, user)</td>
    <td>If executed by the superuser, changes file owner to stated owner. Inoperative but
    returns true on Windows systems.</td>
  </tr>
  <tr>
    <td>clearstatcache</td>
    <td>Clears cache of file status info.</td>
  </tr>
  <tr>
    <td>copy(file, destination)</td>
    <td>Copies file to stated destination.</td>
  </tr>
  <tr>
    <td>delete(file)</td>
    <td>See "unlink."</td>
  </tr>
  <tr>
    <td>dirname(path)</td>
    <td>Returns the directory portion of a stated path.</td>
  </tr>
  <tr>
    <td>disk_free_space("/dir")</td>
    <td>Returns the number of free bytes in a given directory.</td>
  </tr>
  <tr>
    <td>fgetcsv(fp, length, delimiter [, enclosure])</td>
    <td>Reads in a line and parses for CSV format.</td>
  </tr>
  <tr>
    <td>fgetss(fp, length [, allowable_tags])</td>
    <td>Gets a file line (delimited by a newline character) and strips all HTML and PHP tags
    except those specifically allowed.</td>
  </tr>
  <tr>
    <td>fileatime(file)</td>
    <td>Returns (and caches) last time of access.</td>
  </tr>
  <tr>
    <td>filectime(file)</td>
    <td>Returns (and caches) last time of inode change.</td>
  </tr>
  <tr>
    <td>filegroup(file)</td>
    <td>Returns (and caches) file group ID number. Names can be determined by using
    posix_getgrgid().</td>
  </tr>
  <tr>
    <td>fileinode(file)</td>
    <td>Returns (and caches) file inode.</td>
  </tr>
  <tr>
    <td>filemtime(file)</td>
    <td>Returns (and caches) last time of modification.</td>
  </tr>
  <tr>
    <td>fileowner(file)</td>
    <td>Returns (and caches) owner ID number. Names can be determined by using
    posix_getpwuid().</td>
  </tr>
  <tr>
    <td>fileperms(file)</td>
    <td>Returns (and caches) file permissions level.</td>
  </tr>
  <tr>
    <td>filetype(file)</td>
    <td>Returns (and caches) one of: fifo, char, dir,
      block, link, file, unknown.</td>
  </tr>
  <tr>
    <td>flock(file, operation [,&amp;wouldblock])</td>
    <td>Advisory file locking. Operation value must be LOCK_SH (shared), LOCK_EX (exclusive),
    LOCK_UN (release), or LOCK_NB (don’t block while locking). The optional third parameter
    is set to true if enforcing the lock would block existing access.</td>
  </tr>
  <tr>
    <td>fpassthru(fp)</td>
    <td>Standard output of all data from file pointer to EOF.</td>
  </tr>
  <tr>
    <td>fseek(fp, offset, whence)</td>
    <td>Moves file pointer offset number of bytes into file stream from the position
    indicated by whence.</td>
  </tr>
  <tr>
    <td>ftell(fp)</td>
    <td>Returns offset position into file stream.</td>
  </tr>
  <tr>
    <td>stream_set_write_buffer (fp [, buffersize])</td>
    <td>Sets a buffer for file writing; the default is 8K.</td>
  </tr>
  <tr>
    <td>Is_dir(directory)</td>
    <td>Returns (and caches) true if named directory exists.</td>
  </tr>
  <tr>
    <td>Is_executable(file)</td>
    <td>Returns (and caches) true if named file is executable.</td>
  </tr>
  <tr>
    <td>Is_file(file)</td>
    <td>Returns (and caches) true if named file is a regular file.</td>
  </tr>
  <tr>
    <td>Is_link(file)</td>
    <td>Returns (and caches) true if named file is a symlink.</td>
  </tr>
  <tr>
    <td>Is_readable(file)</td>
    <td>Returns (and caches) true if named file is readable by PHP.</td>
  </tr>
  <tr>
    <td>is_writable (file/directory)</td>
    <td>Returns (and caches) true if named file or directory is writable by PHP.</td>
  </tr>
  <tr>
    <td>link(target, link)</td>
    <td>Creates hard link. Inoperative on Windows systems.</td>
  </tr>
  <tr>
    <td>linkinfo(path)</td>
    <td>Confirms existence of link. Inoperative on Windows systems.</td>
  </tr>
  <tr>
    <td>mkdir(path, mode)</td>
    <td>Makes directory at location path with the given permissions in octal mode.</td>
  </tr>
  <tr>
    <td>pclose(fp)</td>
    <td>Closes process file pointer opened by popen().</td>
  </tr>
  <tr>
    <td>popen(command, mode)</td>
    <td>Opens process file pointer.</td>
  </tr>
  <tr>
    <td>readlink(link)</td>
    <td>Returns target of a symlink. Inoperative on Windows systems.</td>
  </tr>
  <tr>
    <td>rename(oldname, newname)</td>
    <td>Renames file.</td>
  </tr>
  <tr>
    <td>rewind(fp)</td>
    <td>Resets file pointer to beginning of file stream.</td>
  </tr>
  <tr>
    <td>rmdir(directory)</td>
    <td>Removes an empty directory.</td>
  </tr>
  <tr>
    <td>stat(file)</td>
    <td>Returns a selection of info about file.</td>
  </tr>
  <tr>
    <td>lstat(file)</td>
    <td>Returns a selection of info about file or symlink.</td>
  </tr>
  <tr>
    <td>symlink(target, link)</td>
    <td>Creates a symlink from target to link. Inoperative on Windows systems.</td>
  </tr>
  <tr>
    <td>touch(file, [time])</td>
    <td>Sets modification time; creates file if it does not exist.</td>
  </tr>
  <tr>
    <td>umask(mask)</td>
    <td>Returns umask, and sets to mask & 0777. With no argument passed, it simply returns
    the umask.</td>
  </tr>
  <tr>
    <td>unlink(file)</td>
    <td>Deletes file.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>