from typing import Union

from fastapi import FastAPI, File, UploadFile, Form, HTTPException
from fastapi.responses import StreamingResponse
from io import BytesIO
import os
import cv2 as cv
import numpy as np
import base64

from generate import generateLabelQr

app = FastAPI()


@app.get("/")
def read_root():
    return {"Hello": "World"}


@app.get("/items/{item_id}")
def read_item(item_id: int, q: Union[str, None] = None):
    return {"item_id": item_id, "q": q}

@app.post("/generate_qr_code")
async def generateQr(text: str = Form(...), image: UploadFile = File(...)):
    imageBytes = await image.read()

    imageBuffer = np.frombuffer(imageBytes, np.uint8)
    img = cv.imdecode(imageBuffer, cv.IMREAD_COLOR)
    if img is None:
        raise HTTPException(status_code=400, detail="Invalid image format")
    
    result = generateLabelQr(img, text)
    
    _, label_encode = cv.imencode('.jpg', result['label'])
    _, qr_encode = cv.imencode('.jpg', result['qr'])

    label_base64 = base64.b64encode(label_encode.tobytes()).decode('utf-8')
    qr_base64 = base64.b64encode(qr_encode.tobytes()).decode('utf-8')

    # return StreamingResponse(BytesIO(label_encode.tobytes()), media_type="image/jpeg")
    return {"label": label_base64, "qr": qr_base64}

@app.get("/testing")
def testing():
    return {"Hello": "World"}

if __name__ == '__main__':
    import uvicorn
    port = int(os.getenv("PORT", 8080))  # Defaults to 8000 if PORT is not set
    uvicorn.run("main:app", host="0.0.0.0", port=port, reload=True)