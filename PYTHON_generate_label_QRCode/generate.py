import cv2 as cv
import numpy as np
import matplotlib.pyplot as plt
from helper import rescaleFrame, hsvConversion, generateQR, labelQR
from fastapi import HTTPException
from PIL import Image

def generateLabelQr(img, text: str = ""):
    img_bgr = cv.cvtColor(img.copy(), cv.COLOR_BGR2RGB)

    # InRange
    lower = np.array(hsvConversion(78,50,50))
    upper = np.array(hsvConversion(150, 100, 100))

    hsv = cv.cvtColor(img, cv.COLOR_BGR2HSV)
    mask = cv.inRange(hsv, lower, upper)
    inRange = cv.bitwise_and(img, img, mask=mask)
    inRange_result = cv.cvtColor(inRange.copy(), cv.COLOR_BGR2RGB)

    contours, hierarchies = cv.findContours(mask, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE)

    contoursImg = img.copy()
    rectangle_detection = np.zeros(img.shape, dtype='uint8')
    rectFound = False

    for contour in contours:
        # Approximate the contour
        epsilon = 0.02 * cv.arcLength(contour, True)
        approx = cv.approxPolyDP(contour, epsilon, True)

        if len(approx) == 4:
            # Get bounding rectangle
            x, y, w, h = cv.boundingRect(approx)
            
            # Aspect ratio check
            aspect_ratio = float(w) / h
            if 0.8 <= aspect_ratio <= 1.2:  # Allow small margin for error
                print("Rectangle Detected")
                cv.rectangle(rectangle_detection, (x, y), (x + w, y + h), (0, 255, 0), -1)
                # cv.drawContours(rectangle_detection, [approx], 0, (0, 255, 0), 2)
                qr = generateQR(text, w, h)
                label = labelQR(img, qr, x, y)

                qr = cv.cvtColor(qr, cv.COLOR_BGR2RGB)
                label = cv.cvtColor(label, cv.COLOR_BGR2RGB)
                rectFound = True
            else:
                print("Not a Rectangle")

    if not rectFound:
        raise HTTPException(status_code=400, detail="Rectangle not found in image, couldn't make label")
        
    # plt.figure()
    # plt.subplot(1, 4, 1), plt.imshow(img_bgr), plt.title('Original Image')
    # plt.subplot(1, 4, 2), plt.imshow(inRange_result), plt.title('inRange Detection')
    # plt.subplot(1, 4, 3), plt.imshow(rectangle_detection), plt.title('Rectangle Detection')
    # plt.subplot(1, 4, 4), plt.imshow(label), plt.title('Label QR Code')
    # plt.show()
    return {
        'label': label,
        'qr': qr
    }