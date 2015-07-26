<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$docRoot = $_SERVER["DOCUMENT_ROOT"];

require_once("$docRoot/web/includes/login_funcs.php");
require_once("$docRoot/web/includes/db_vars.php");
require_once("$docRoot/web/includes/header_footer.php");

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


site_header("Character Encoding");

$page_str = <<<EOPAGESTR5

<p>I read a very enlightening article on the web about this topic. It is
by Joel Spolsky. It's called "The Absolute Minimum Every Software Developer
Absolutely, Positively Must Know About Unicode and Character Sets (No
Excuses!)." Here is a link to it:</p>

<p><a href="http://www.joelonsoftware.com/articles/Unicode.html">Link</a></p>

<p>Basically what I got out of the article is that someone who uses the English
language like me can go on programming PHP in complete blissful ignorance
of encoding as long as he uses UTF-8, ASCII, ISO-8859-1 or Latin-1 encoded files
which ONLY contain characters in the 32 - 127 range. The reason is that in this
range each one of these encodings uses one byte to represent a character and
has the same binary value stored in that byte.</p>

<div class="remarkbox">
  <div class="rbtitle">note-to-self</div>
  <div class="rbcontent">
  <p>To stay out of trouble when dealing with HTML follow these rules:
  One, use UTF-8 with no BOM. Two, don't include any character in my
  file that is not in the 32 - 127 range. Three, if I want my HTML page
  to display a character outside this range then use an HTML entity
  sequence to do so. For example: a fancy opening double quote character
  is decimal 8220 code point in Unicode. I would use the HTML entity
  &amp;#8220; instead of just cut and pasting it into the file.</p>
  <p>Otherwise, I just have to
  realize (when iterating through a UTF-8 byte stream)
  that SOME characters occupy more than one byte (up-to four).
  PHP has the ability to tell me if
  a byte is a character or not. If it is not
  then that byte is part of a multibyte
  character. I should
  be able to iterate backwards or forwards to find the beginning and
  end bytes of this multibyte character. I just have to understand how UTF-8
  encodes characters and use mbstring PHP functions.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Caution</div>
  <div class="rbcontent">
  
  <p>Validate a UTF-8 stream before using it to prevent strange problems.
  There are many ways UTF-8 source can become corrupted.</p>
  
  <p>Many Windows programs (including Windows Notepad) add the bytes 0xEF, 0xBB,
  0xBF at the start of any document saved as UTF-8. This is the UTF-8 encoding
  of the Unicode byte order mark (BOM), and is commonly referred to as a UTF-8
  BOM, even though it is not relevant to byte order. The BOM can also appear if
  another encoding with a BOM is translated to UTF-8 without stripping it.</p>
  <p>The presence of the UTF-8 BOM may cause interoperability problems with
  existing software that could otherwise handle UTF-8. Programs that insert
  information at the start of a file will result in a file with the BOM
  somewhere in the middle of it. One example is offline browsers that add the
  originating URL to the start of the file.</p>
  <p>Programs that identify file types by leading characters may fail to
  identify the file if a BOM is present even if the user of the file could skip
  the BOM. Or conversely they will identify the file when the user cannot handle
  the BOM. An example is the Unix shebang syntax.</p>
  <p>If compatibility with existing programs is not important, the BOM could be
  used to identify if a file is in UTF-8 versus a legacy encoding, but this is
  still problematic, due to many instances where the BOM is added or removed
  without actually changing the encoding, or various encodings are concatenated
  together. Checking if the text is valid UTF-8 is more reliable than using
  BOM.</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Definition</div>
  <div class="rbcontent">
  <p>"Code point" is a term which means a number associated
  with the encoding of a character. No matter how many bytes, or
  how they are arranged in these bytes, or what special value
  bytes are prepended to the encoded file or string, all encoding
  schemes use code point values. The characters which overlap
  among the encoding schemes will most likely have the same
  code point values associated with them (especially in the 32 - 127 range).</p>
  </div>
</div>

<div class="remarkbox">
  <div class="rbtitle">Note</div>
  <div class="rbcontent">
  <p>The "code point (Hex. Octets)" field in table below does not
  contain the actual UTF-8 encoding. It is simply a hexadecimal octet
  representation of the code point. The way a code point would be
  specified in official documentation would be like this for the
  character 7: U+0037. In my table I specify it as 37 in the
  "code point (Hex. Octets)" table field. See section called UTF-8
  below for an explanation of how UTF-8 encoding of memory bytes
  is done.</p>
  </div>
</div>

<p>Here are some code points and the characters they represent:</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>code point (Dec.)</th>
    <th>code point (Hex.)</th>
    <th>Character</th>
  </tr>
  <tr>
    <td>0</td>
    <td>x0000</td>
    <td>Null character</td>
  </tr>
  <tr>
    <td>4</td>
    <td>x0004</td>
    <td>End Of Transmission</td>
  </tr>
  <tr>
    <td>9</td>
    <td>x0009</td>
    <td>tab</td>
  </tr>
  <tr>
    <td>10</td>
    <td>x000A</td>
    <td>line feed</td>
  </tr>
  <tr>
    <td>13</td>
    <td>x000D</td>
    <td>carriage return</td>
  </tr>
  <tr>
    <td>32</td>
    <td>x0020</td>
    <td>Space</td>
  </tr>
  <tr>
    <td>33</td>
    <td>x0021</td>
    <td>&#33;</td>
  </tr>
  <tr>
    <td>34</td>
    <td>x0022</td>
    <td>&quot;</td>
  </tr>
  <tr>
    <td>35</td>
    <td>x0023</td>
    <td>#</td>
  </tr>
  <tr>
    <td>36</td>
    <td>x0024</td>
    <td>&#36;</td>
  </tr>
  <tr>
    <td>37</td>
    <td>x0025</td>
    <td>%</td>
  </tr>
  <tr>
    <td>38</td>
    <td>x0026</td>
    <td>&amp;</td>
  </tr>
  <tr>
    <td>39</td>
    <td>x0027</td>
    <td>&#39; (keyboard apostrophe)</td>
  </tr>
  <tr>
    <td>40</td>
    <td>x0028</td>
    <td>(</td>
  </tr>
  <tr>
    <td>41</td>
    <td>x0029</td>
    <td>)</td>
  </tr>
  <tr>
    <td>42</td>
    <td>x002A</td>
    <td>*</td>
  </tr>
  <tr>
    <td>43</td>
    <td>x002B</td>
    <td>+</td>
  </tr>
  <tr>
    <td>44</td>
    <td>x002C</td>
    <td>,</td>
  </tr>
  <tr>
    <td>45</td>
    <td>x002D</td>
    <td>-</td>
  </tr>
  <tr>
    <td>46</td>
    <td>x002E</td>
    <td>.</td>
  </tr>
  <tr>
    <td>47</td>
    <td>x002F</td>
    <td>/</td>
  </tr>
  <tr>
    <td>48</td>
    <td>x0030</td>
    <td>0</td>
  </tr>
  <tr>
    <td>49</td>
    <td>x0031</td>
    <td>1</td>
  </tr>
  <tr>
    <td>50</td>
    <td>x0032</td>
    <td>2</td>
  </tr>
  <tr>
    <td>51</td>
    <td>x0033</td>
    <td>3</td>
  </tr>
  <tr>
    <td>52</td>
    <td>x0034</td>
    <td>4</td>
  </tr>
  <tr>
    <td>53</td>
    <td>x0035</td>
    <td>5</td>
  </tr>
  <tr>
    <td>54</td>
    <td>x0036</td>
    <td>6</td>
  </tr>
  <tr>
    <td>55</td>
    <td>x0037</td>
    <td>7</td>
  </tr>
  <tr>
    <td>56</td>
    <td>x0038</td>
    <td>8</td>
  </tr>
  <tr>
    <td>57</td>
    <td>x0039</td>
    <td>9</td>
  </tr>
  <tr>
    <td>58</td>
    <td>x003A</td>
    <td>:</td>
  </tr>
  <tr>
    <td>59</td>
    <td>x003B</td>
    <td>;</td>
  </tr>
  <tr>
    <td>60</td>
    <td>x003C</td>
    <td>&lt;</td>
  </tr>
  <tr>
    <td>61</td>
    <td>x003D</td>
    <td>=</td>
  </tr>
  <tr>
    <td>62</td>
    <td>x003E</td>
    <td>&gt;</td>
  </tr>
  <tr>
    <td>63</td>
    <td>x003F</td>
    <td>?</td>
  </tr>
  <tr>
    <td>64</td>
    <td>x0040</td>
    <td>@</td>
  </tr>
  <tr>
    <td>65</td>
    <td>x0041</td>
    <td>A</td>
  </tr>
  <tr>
    <td>66</td>
    <td>x0042</td>
    <td>B</td>
  </tr>
  <tr>
    <td>67</td>
    <td>x0043</td>
    <td>C</td>
  </tr>
  <tr>
    <td>68</td>
    <td>x0044</td>
    <td>D</td>
  </tr>
  <tr>
    <td>69</td>
    <td>x0045</td>
    <td>E</td>
  </tr>
  <tr>
    <td>70</td>
    <td>x0046</td>
    <td>F</td>
  </tr>
  <tr>
    <td>71</td>
    <td>x0047</td>
    <td>G</td>
  </tr>
  <tr>
    <td>72</td>
    <td>x0048</td>
    <td>H</td>
  </tr>
  <tr>
    <td>73</td>
    <td>x0049</td>
    <td>I</td>
  </tr>
  <tr>
    <td>74</td>
    <td>x004A</td>
    <td>J</td>
  </tr>
  <tr>
    <td>75</td>
    <td>x004B</td>
    <td>K</td>
  </tr>
  <tr>
    <td>76</td>
    <td>x004C</td>
    <td>L</td>
  </tr>
  <tr>
    <td>77</td>
    <td>x004D</td>
    <td>M</td>
  </tr>
  <tr>
    <td>78</td>
    <td>x004E</td>
    <td>N</td>
  </tr>
  <tr>
    <td>79</td>
    <td>x004F</td>
    <td>O</td>
  </tr>
  <tr>
    <td>80</td>
    <td>x0050</td>
    <td>P</td>
  </tr>
  <tr>
    <td>81</td>
    <td>x0051</td>
    <td>Q</td>
  </tr>
  <tr>
    <td>82</td>
    <td>x0052</td>
    <td>R</td>
  </tr>
  <tr>
    <td>83</td>
    <td>x0053</td>
    <td>S</td>
  </tr>
  <tr>
    <td>84</td>
    <td>x0054</td>
    <td>T</td>
  </tr>
  <tr>
    <td>85</td>
    <td>x0055</td>
    <td>U</td>
  </tr>
  <tr>
    <td>86</td>
    <td>x0056</td>
    <td>V</td>
  </tr>
  <tr>
    <td>87</td>
    <td>x0057</td>
    <td>W</td>
  </tr>
  <tr>
    <td>88</td>
    <td>x0058</td>
    <td>X</td>
  </tr>
  <tr>
    <td>89</td>
    <td>x0059</td>
    <td>Y</td>
  </tr>
  <tr>
    <td>90</td>
    <td>x005A</td>
    <td>Z</td>
  </tr>
  <tr>
    <td>91</td>
    <td>x005B</td>
    <td>[</td>
  </tr>
  <tr>
    <td>92</td>
    <td>x005C</td>
    <td>&#92;</td>
  </tr>
  <tr>
    <td>93</td>
    <td>x005D</td>
    <td>]</td>
  </tr>
  <tr>
    <td>94</td>
    <td>x005E</td>
    <td>^</td>
  </tr>
  <tr>
    <td>95</td>
    <td>x005F</td>
    <td>_</td>
  </tr>
  <tr>
    <td>96</td>
    <td>x0060</td>
    <td>&#96; (keyboard backtick)</td>
  </tr>
  <tr>
    <td>97</td>
    <td>x0061</td>
    <td>a</td>
  </tr>
  <tr>
    <td>98</td>
    <td>x0062</td>
    <td>b</td>
  </tr>
  <tr>
    <td>99</td>
    <td>x0063</td>
    <td>c</td>
  </tr>
  <tr>
    <td>100</td>
    <td>x0064</td>
    <td>d</td>
  </tr>
  <tr>
    <td>101</td>
    <td>x0065</td>
    <td>e</td>
  </tr>
  <tr>
    <td>102</td>
    <td>x0066</td>
    <td>f</td>
  </tr>
  <tr>
    <td>103</td>
    <td>x0067</td>
    <td>g</td>
  </tr>
  <tr>
    <td>104</td>
    <td>x0068</td>
    <td>h</td>
  </tr>
  <tr>
    <td>105</td>
    <td>x0069</td>
    <td>i</td>
  </tr>
  <tr>
    <td>106</td>
    <td>x006A</td>
    <td>j</td>
  </tr>
  <tr>
    <td>107</td>
    <td>x006B</td>
    <td>k</td>
  </tr>
  <tr>
    <td>108</td>
    <td>x006C</td>
    <td>l</td>
  </tr>
  <tr>
    <td>109</td>
    <td>x006D</td>
    <td>m</td>
  </tr>
  <tr>
    <td>110</td>
    <td>x006E</td>
    <td>n</td>
  </tr>
  <tr>
    <td>111</td>
    <td>x006F</td>
    <td>o</td>
  </tr>
  <tr>
    <td>112</td>
    <td>x0070</td>
    <td>p</td>
  </tr>
  <tr>
    <td>113</td>
    <td>x0071</td>
    <td>q</td>
  </tr>
  <tr>
    <td>114</td>
    <td>x0072</td>
    <td>r</td>
  </tr>
  <tr>
    <td>115</td>
    <td>x0073</td>
    <td>s</td>
  </tr>
  <tr>
    <td>116</td>
    <td>x0074</td>
    <td>t</td>
  </tr>
  <tr>
    <td>117</td>
    <td>x0075</td>
    <td>u</td>
  </tr>
  <tr>
    <td>118</td>
    <td>x0076</td>
    <td>v</td>
  </tr>
  <tr>
    <td>119</td>
    <td>x0077</td>
    <td>w</td>
  </tr>
  <tr>
    <td>120</td>
    <td>x0078</td>
    <td>x</td>
  </tr>
  <tr>
    <td>121</td>
    <td>x0079</td>
    <td>y</td>
  </tr>
  <tr>
    <td>122</td>
    <td>x007A</td>
    <td>z</td>
  </tr>
  <tr>
    <td>123</td>
    <td>x007B</td>
    <td>{</td>
  </tr>
  <tr>
    <td>124</td>
    <td>x007C</td>
    <td>|</td>
  </tr>
  <tr>
    <td>125</td>
    <td>x007D</td>
    <td>}</td>
  </tr>
  <tr>
    <td>126</td>
    <td>x007E</td>
    <td>~</td>
  </tr>
  <tr>
    <td>127</td>
    <td>x007F</td>
    <td>keyboard backspace</td>
  </tr>
  <tr>
    <td>160</td>
    <td>x00A0</td>
    <td>non-break space</td>
  </tr>
  <tr>
    <td>181</td>
    <td>x00B5</td>
    <td>&#181;</td>
  </tr>
  <tr>
    <td>216</td>
    <td>x00B5</td>
    <td>&#216;</td>
  </tr>
  <tr>
    <td>248</td>
    <td>x00F8</td>
    <td>&#248;</td>
  </tr>
  <tr>
    <td>223</td>
    <td>x00DF</td>
    <td>&#223;</td>
  </tr>
  <tr>
    <td>183</td>
    <td>x00B7</td>
    <td>&#183;</td>
  </tr>
  <tr>
    <td>166</td>
    <td>x00A6</td>
    <td>&#166;</td>
  </tr>
  <tr>
    <td>171</td>
    <td>x00AB</td>
    <td>&#171;</td>
  </tr>
  <tr>
    <td>187</td>
    <td>x00BB</td>
    <td>&#187;</td>
  </tr>
  <tr>
    <td>182</td>
    <td>x00B6</td>
    <td>&#182; (paragraph sign)</td>
  </tr>
  <tr>
    <td>169</td>
    <td>x00A9</td>
    <td>&#169;</td>
  </tr>
  <tr>
    <td>174</td>
    <td>x00AE</td>
    <td>&#174;</td>
  </tr>
  <tr>
    <td>215</td>
    <td>x00D7</td>
    <td>&#215;</td>
  </tr>
  <tr>
    <td>247</td>
    <td>x00F7</td>
    <td>&#247;</td>
  </tr>
  <tr>
    <td>188</td>
    <td>x00BC</td>
    <td>&#188;</td>
  </tr>
  <tr>
    <td>189</td>
    <td>x00BD</td>
    <td>&#189;</td>
  </tr>
  <tr>
    <td>190</td>
    <td>x00BE</td>
    <td>&#190;</td>
  </tr>
  <tr>
    <td>176</td>
    <td>x00B0</td>
    <td>&#176;</td>
  </tr>
  <tr>
    <td>177</td>
    <td>x00B1</td>
    <td>&#177;</td>
  </tr>
  <tr>
    <td>162</td>
    <td>x00A2</td>
    <td>&#162;</td>
  </tr>
  <tr>
    <td>163</td>
    <td>x00A3</td>
    <td>&#163;</td>
  </tr>
  <tr>
    <td>165</td>
    <td>x00A5</td>
    <td>&#165;</td>
  </tr>
  <tr>
    <td>916</td>
    <td>x0394</td>
    <td>&#916;</td>
  </tr>
  <tr>
    <td>937</td>
    <td>x03A9</td>
    <td>&#937;</td>
  </tr>
  <tr>
    <td>733</td>
    <td>x02DD</td>
    <td>&#733;</td>
  </tr>
  <tr>
    <td>8211</td>
    <td>x2013</td>
    <td>&#8211;</td>
  </tr>
  <tr>
    <td>8212</td>
    <td>x2014</td>
    <td>&#8212;</td>
  </tr>
  <tr>
    <td>8226</td>
    <td>x2022</td>
    <td>&#8226;</td>
  </tr>
  <tr>
    <td>8230</td>
    <td>x2026</td>
    <td>&#8230;</td>
  </tr>
  <tr>
    <td>8216</td>
    <td>x2018</td>
    <td>&#8216;</td>
  </tr>
  <tr>
    <td>8217</td>
    <td>x2019</td>
    <td>&#8217;</td>
  </tr>
  <tr>
    <td>8218</td>
    <td>x201A</td>
    <td>&#8218; (fancy comma)</td>
  </tr>
  <tr>
    <td>8220</td>
    <td>x201C</td>
    <td>&#8220;</td>
  </tr>
  <tr>
    <td>8221</td>
    <td>x201D</td>
    <td>&#8221;</td>
  </tr>
  <tr>
    <td>8250</td>
    <td>x203A</td>
    <td>&#8250;</td>
  </tr>
  <tr>
    <td>8482</td>
    <td>x2122</td>
    <td>&#8482;</td>
  </tr>
  <tr>
    <td>8730</td>
    <td>x221A</td>
    <td>&#8730;</td>
  </tr>
  <tr>
    <td>8734</td>
    <td>x221E</td>
    <td>&#8734;</td>
  </tr>
  <tr>
    <td>8747</td>
    <td>x222B</td>
    <td>&#8747;</td>
  </tr>
  <tr>
    <td>8706</td>
    <td>x2202</td>
    <td>&#8706;</td>
  </tr>
  <tr>
    <td>8773</td>
    <td>x2245</td>
    <td>&#8773;</td>
  </tr>
  <tr>
    <td>8800</td>
    <td>x2260</td>
    <td>&#8800;</td>
  </tr>
  <tr>
    <td>8804</td>
    <td>x2264</td>
    <td>&#8804;</td>
  </tr>
  <tr>
    <td>8805</td>
    <td>x2265</td>
    <td>&#8805;</td>
  </tr>
  <tr>
    <td>931</td>
    <td>x03A3</td>
    <td>&#931;</td>
  </tr>
  <tr>
    <td>8260</td>
    <td>x2044</td>
    <td>&#8260;</td>
  </tr>
  <tr>
    <td>960</td>
    <td>x03C0</td>
    <td>&pi; (pi symbol)</td>
  </tr>
  <tr>
    <td>9774</td>
    <td>x262E</td>
    <td>&#9774; (peace symbol)</td>
  </tr>
</table>

<h2>UTF-8</h2>

<p><a href="http://en.wikipedia.org/wiki/UTF-8">Link to source of this
information.</a></p>

<p>UTF-8 (UCS Transformation Format &#8211; 8-bit) is a multibyte character
encoding for Unicode. Like UTF-16 and UTF-32, UTF-8 can represent every
character in the Unicode character set. Unlike them, it is backward-compatible
with ASCII and avoids the complications of endianness and byte order marks
(BOM). For these and other reasons, UTF-8 has become the dominant character
encoding for the World-Wide Web, &#8230;.</p>

<p>UTF-8 encodes each of the 1,112,064 code points in the Unicode character set
using one to four 8-bit bytes (termed &#8220;octets&#8221; in the Unicode
Standard). Code points with lower numerical values (i. e., earlier code
positions in the Unicode character set, which tend to occur more frequently in
practice) are encoded using fewer bytes, making the encoding scheme reasonably
efficient. In particular, the first 128 characters of the Unicode character set,
which correspond one-to-one with ASCII, are encoded using a single octet with
the same binary value as the corresponding ASCII character, making valid ASCII
text valid UTF-8-encoded Unicode text as well.</p>

<h3>Design</h3>

<p>The design of UTF‑8 as originally proposed by Dave Prosser and subsequently
modified by Ken Thompson was intended to satisfy two objectives:</p>

<ol>
  <li>To be backward-compatible with ASCII; and</li>
  <li>To enable encoding of up to at least 2<sup>31</sup> characters (the
  theoretical limit of the first draft proposal for the Universal Character
  Set).</li>
</ol>

<p>Being backward-compatible with ASCII implied that every valid ASCII
character (a 7-bit character set) also be a valid UTF‑8 character sequence,
specifically, a one-byte UTF‑8 character sequence whose binary value equals
that of the corresponding ASCII character:</p>

<div>
  <img src="utf_wiki_iOne.jpg" width="175" height="50" alt="table showing byte
  unicode" />
</div>

<p>Prosser’s and Thompson’s challenge was to extend this scheme to handle code
points with up to 31 bits.  The solution proposed by Prosser as subsequently
modified by Thompson was as follows:</p>

<div>
  <img src="utf_wiki_iTwo.jpg" width="447" height="168" alt="proposed
  solution" />
</div>

<p>The salient features of the above scheme are as follows:</p>

<ol>
  <li>Every valid ASCII character is also a valid UTF‑8 encoded Unicode
  character with the same binary value.  (Thus, valid ASCII text is also valid
  UTF‑8-encoded Unicode text.)</li>
  <li>For every UTF‑8 byte sequence corresponding to a single Unicode character,
  the first byte unambiguously indicates the length of the sequence in
  bytes.</li>
  <li>All continuation bytes (byte nos. 2 – 6 in the table above) have 10 as
  their two most-significant bits (bits 7 – 6); in contrast, the first byte
  never has 10 as its two most-significant bits.  As a result, it is immediately
  obvious whether any given byte anywhere in a (valid) UTF‑8 stream represents
  the first byte of a byte sequence corresponding to a single character, or a
  continuation byte of such a byte sequence.</li>
  <li>As a consequence of no. 3 above, starting with any arbitrary byte anywhere
  in a (valid) UTF‑8 stream, it is necessary to back up by only at most five
  bytes in order to get to the beginning of the byte sequence corresponding to a
  single character (three bytes in actual UTF‑8 as explained in the next
  section). If it is not possible to back up, or a byte is missing because of
  e.g. a communication failure, one single character can be discarded, and the
  next character be correctly read.</li>
  <li>Starting with the second row in the table above (two bytes), every
  additional byte extends the maximum number of bits by five (six additional
  bits from the additional continuation byte, minus one bit lost in the first
  byte).</li>
  <li>Prosser’s and Thompson’s scheme was sufficiently general to be extended
  beyond 6-byte sequences (however, this would have allowed FE or FF bytes to
  occur in valid UTF-8 text — see under Advantages in section "Compared to
  single byte encodings" below — and indefinite extension would lose the
  desirable feature that the length of a sequence can be determined from the
  start byte only).</li>
</ol>

<h3>Description</h3>

<p>UTF-8 is a variable-width encoding, with each character represented by one
to four bytes. If the character is encoded by just one byte, the high-order bit
is 0 and the other bits give the code value (in the range 0..127). If the
character is encoded by a sequence of more than one byte, the first byte has as
many leading '1' bits as the total number of bytes in the sequence, followed by
a '0' bit, and the succeeding bytes are all marked by a leading "10" bit
pattern. The remaining bits in the byte sequence are concatenated to form the
Unicode code point value (in the range 80<sub>hex</sub> to
10FFFF<sub>hex)</sub>. Thus a byte with
lead bit '0' is a single-byte code, a byte with multiple leading '1' bits is the
first of a multi-byte sequence, and a byte with a leading "10" bit pattern is a
continuation byte of a multi-byte sequence. The format of the bytes thus allows
the beginning of each sequence to be detected without decoding from the
beginning of the string. UTF-16 limits Unicode to 10FFFF<sub>hex</sub>;
therefore UTF-8 is
not defined beyond that value, even if it could easily be defined to reach
7FFFFFFF<sub>hex</sub>.</p>

<p>So the first 128 characters (US-ASCII) need one byte. The next 1,920
characters need two bytes to encode. This includes Latin letters with
diacritics and characters from the Greek, Cyrillic, Coptic, Armenian, Hebrew,
Arabic, Syriac and Tāna alphabets. Three bytes are needed for the rest of the
Basic Multilingual Plane (which contains virtually all characters in common
use). Four bytes are needed for characters in the other planes of Unicode,
which include less common CJK characters and various historic scripts.</p>

<p>Here are the valid characters expressed in hexadecimal format and include
the following range of characters:</p>

<blockquote>09 , 0A , 0D , [20-D7FF] , [E000-FFFD] , [10000-10FFFF]</blockquote>

EOPAGESTR5;
echo $page_str;

site_footer();

?>