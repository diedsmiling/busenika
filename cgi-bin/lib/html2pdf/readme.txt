The steps described below can be used in order to install any custom font into html2pdf library.
The procedure is for Tahoma font (please refer to http://www.tufat.com/docs/html2ps/howto_fonts.html#fpdf for detailed instructions).

- copy font files (tahoma.ttf, tahomabd.ttf) to TTF_FONTS_REPOSITORY directory (by default it points to 'fonts' subdirectory)
- generate font metrics file for these fonts files, you can use the Windows ttf2pt1.exe program
  ( http://www.fpdf.org/download/ttf2pt1.zip) 
  for the .afm file generation:
	*copy the tahoma.ttf file to the folder where the ttf2pt1.exe file is
	*perform the following command in the Windows command line  
		ttf2pt1.exe -aGfAe tahoma.ttf tahoma
		
  and put the .afm file into the same directory. Note that metrics file should have the same name as font file and 
  extension .afm (this step is optional if you're using a html2ps version more recent than 1.9.4):		
		
- register new font family in html2ps.config: add the following lines to html2ps.config in the FONTS-PDF section (between <fonts-pdf> and </fonts-pdf> tags):

        <family name="tahoma">
          <normal normal="Tahoma" italic="Tahoma-Italic" oblique="Tahoma-Italic"/>
          <bold normal="Tahoma-Bold" italic="Tahoma-Bold-Italic" oblique="Tahoma-Bold-Italic"/>
        </family>

- register font files: add the following to the FONTS-PDF section:

        <ttf typeface="Tahoma"             embed="0" file="tahoma.ttf"/>
        <ttf typeface="Tahoma-Bold"        embed="0" file="tahomabd.ttf"/>
  

   note that you may want to replace embed="0" with embed="1" if you intend to distribute generated PDF to users without Trebuchet MS font installed in their machines.

 

