# -*- coding:utf-8 -*-
import shutil
from pychartdir import *
from settings import STATIC_PATH, PNG_KEEP_TIME
import os


#utility to call DLL method
def getChartUrl(data, labels, title, ytitle, item):

        # Create an XYChart object of size 600 x 360 pixels, with a light blue (EEEEFF)
        # background, black border, 1 pxiel 3D border effect and rounded corners
        c = XYChart(600, 380, '0xeeeeff', '0x000000', 1)

        c.setRoundedFrame()

        # Set plotarea at (55, 60) with size of 520 x 240 pixels. Use white (ffffff) as
        # background and grey (cccccc) for grid lines
        c.setPlotArea(55, 60, 520, 240, '0xffffff', -1, -1, '0xcccccc', '0xcccccc')

        # Add a legend box at (55, 58) (top of plot area) using 9 pts Arial Bold font with
        # horizontal layout Set border and background colors of the legend box to Transparent
        legendBox = c.addLegend(55, 58, 0, "arialbd.ttf", 9)
        legendBox.setBackground(Transparent)

        # Reserve 10% margin at the top of the plot area during auto-scaling to leave space
        # for the legends.
        c.yAxis().setAutoScale(0.1)

        # Add a title to the chart using 15 pts Times Bold Italic font. The text is white
        # (ffffff) on a blue (0000cc) background, with glass effect.
        title = c.addTitle(title, "simsun.ttc", 12,
            '0xffffff')
        title.setBackground('0x0000cc', '0x000000', glassEffect(ReducedGlare))

        # Add a title to the y axis
        c.yAxis().setTitle(ytitle, "simsun.ttc", 10)

        # Set the labels on the x axis. Draw the labels vertical (angle = 90)
        c.xAxis().setLabels(labels).setFontAngle(90)
        # Add a line layer to the chart
        layer = c.addLineLayer()

        # Set the default line width to 3 pixels
        layer.setLineWidth(3)

        layer.addDataSet(data, -1, "<*font=simsun.ttc,size=10*>" + item)
        #layer.addDataSet(data1, -1, "<*font=simsun.ttc,size=10*>" + item2)

        fileLst = os.listdir(STATIC_PATH + "/tmp")

        if len(fileLst) > 0:
           for file in fileLst:
               print str(os.path.getatime(STATIC_PATH + "/tmp/" + file))
               if (os.path.getatime(STATIC_PATH + "/tmp/" + file) - time.time() > PNG_KEEP_TIME):
                    print "file=" + STATIC_PATH + "/tmp/" + file
                    os.remove(STATIC_PATH + "/tmp/" + file)

        # Create the image
        chartURL = c.makeTmpFile(STATIC_PATH + "/tmp")

        return  chartURL
