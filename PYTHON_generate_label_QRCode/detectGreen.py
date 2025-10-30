import cv2 as cv
import numpy as np
from helper import rescaleFrame, interpolationEnum

img = cv.imread('Sticker3.png')
img = rescaleFrame(img, 0.7)
cv.imshow('Image', img)

hsv = cv.cvtColor(img, cv.COLOR_BGR2HSV)

# Green color
lower_green = np.array([70, 50, 50])
upper_green = np.array([85, 255, 255])

# Get the green color
mask = cv.inRange(hsv, lower_green, upper_green)
cv.imshow('Mask', mask)

# Apply the mask to the original image
result = cv.bitwise_and(img, img, mask=mask)
cv.imshow('Result', result)

# Find contours of the green areas
contours, _ = cv.findContours(mask, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE)

for contour in contours:
    x, y, w, h = cv.boundingRect(contour)
    cv.rectangle(img, (x, y), (x + w, y + h), (0, 255, 0), 1)

cv.imshow('Contours', img)

cv.waitKey(0)
cv.destroyAllWindows()