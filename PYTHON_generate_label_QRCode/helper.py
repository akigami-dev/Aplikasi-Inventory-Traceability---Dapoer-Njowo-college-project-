import cv2 as cv
import numpy as np
from enum import Enum
from PIL import Image
import qrcode
from datetime import datetime

class interpolationEnum(Enum):
    # DOWNSCALING
    INTER_AREA = cv.INTER_AREA
    INTER_NEAREST = cv.INTER_NEAREST

    # UPSCALING
    INTER_LINEAR = cv.INTER_LINEAR
    INTER_CUBIC = cv.INTER_CUBIC
    INTER_LANCZOS4 = cv.INTER_LANCZOS4

def rescaleFrame(frame, scale=0.5, intepolation: interpolationEnum = interpolationEnum.INTER_AREA):
    width = int(frame.shape[1] * scale)
    height = int(frame.shape[0] * scale)
    dimensions = (width, height)

    return cv.resize(frame, dimensions, interpolation=intepolation.value)

def testing(interpolation: interpolationEnum = interpolationEnum.INTER_AREA):
    return interpolation.value

def hsvConversion(h: int, s: int, v: int):
    H_opencv = int(h / 360 * 179)
    S_opencv = int(s / 100 * 255)
    V_opencv = int(v / 100 * 255)

    return [H_opencv, S_opencv, V_opencv]

def generateQR(text: str, width: int, height: int):
    qr_data = text
    qr = qrcode.QRCode(
        version=3,
        error_correction=qrcode.constants.ERROR_CORRECT_M,
        box_size=10,
        border=4,
    )
    qr.add_data(qr_data)
    qr.make(fit=True)
    qr_img = qr.make_image(fill='black', back_color='white').convert("RGBA")
    qr_img = qr_img.resize((width, height))
    # qr_img.save(f"qr_code_{datetime.now().strftime('%Y%m%d%H%M%S')}.png") # Save the image
    return np.array(qr_img)

def labelQR(image: Image, qr: Image, x_pos: int, y_pos: int):
    pil_image = Image.fromarray(cv.cvtColor(image, cv.COLOR_BGR2RGB))
    pil_qr = Image.fromarray(cv.cvtColor(qr, cv.COLOR_BGR2RGB))
    pil_image.paste(pil_qr, (x_pos, y_pos))
    # pil_image.save(f"labelled_{datetime.now().strftime('%Y%m%d%H%M%S')}.png") # Save the image
    return np.array(pil_image)
