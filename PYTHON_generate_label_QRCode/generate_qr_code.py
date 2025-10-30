from PIL import Image
import qrcode
from datetime import datetime

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
    qr_img.save(f"qr_code_{datetime.now().strftime('%Y%m%d%H%M%S')}.png")
    return qr_img


    

# Step 1: Generate the QR code image
qr_data = "http://localhost:8000/trace/PRD192803091283"
qr = qrcode.QRCode(
    version=3,
    error_correction=qrcode.constants.ERROR_CORRECT_M,
    box_size=10,
    border=4,
)
qr.add_data(qr_data)
qr.make(fit=True)
qr_img = qr.make_image(fill='black', back_color='white').convert("RGBA")

qr_img = qr_img.resize((150, 150)) # Resize the QR code image
qr_img.save("qr_code.png")

sticker = Image.open("Sticker.png")

# Define width and height
qr_width, qr_height = qr_img.size
sticker_width, sticker_height = sticker.size

# choose a fixed place
x_pos = sticker_width - qr_width - 10 # 10px margin from the right
y_pos = sticker_height - qr_height - 10  # 10px margin from the bottom

# Step 4: Paste the QR code onto the labelling sticker
sticker.paste(qr_img, (10, y_pos))

# Step 5: Save the new image with the QR code
sticker.save("labelling_with_qr.png")

# Optional: Show the image with the QR code placed on the sticker
sticker.show()