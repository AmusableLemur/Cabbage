from flask import Flask, abort, render_template, request, send_file
from os import listdir
from os.path import isfile, join
from PIL import Image, ImageOps
from math import ceil

app = Flask(__name__)

@app.route("/", defaults={"page": 1})
@app.route("/page/<int:page>")
def index(page):
    files = [f for f in listdir("collection") if isfile(join("collection", f))]
    images = files[((page - 1) * 12):(page * 12)]
    pages = (int)(ceil(len(files) / 12.0)) # Float to make sure ceil() works as intended
    return render_template("index.html", images=images, currentPage=page, totalPages=pages)

@app.route("/image/<image>")
def image(image):
    try:
        return send_file("collection/" + image)
    except IOError:
        abort(404)

@app.route("/thumbnail/<image>")
def thumbnail(image):
    try:
        return send_file("thumbnails/" + image)
    except IOError:
        try:
            thumbnail = Image.open("collection/" + image)
            thumbnail = ImageOps.fit(thumbnail, (210, 210), Image.ANTIALIAS)
            thumbnail.save("thumbnails/" + image)
            return send_file("thumbnails/" + image)
        except IOError:
            abort(404)

@app.route("/view/<image>")
def view(image):
    return render_template("view.html", image=image)

if __name__ == "__main__":
    app.debug = True
    app.run()
