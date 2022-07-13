FROM python:3-alpine

LABEL maintainer="xnetcat (Jakub)"

# Install dependencies
RUN apk add --no-cache \
    ca-certificates \
    ffmpeg \
    openssl \
    aria2 \
    g++ \
    git \
    py3-cffi \
    libffi-dev \
    zlib-dev

# Install poetry and update pip/wheel
RUN pip install --upgrade pip poetry wheel
RUN pip3 install spotipy

# Add source code files to WORKDIR
ADD . .

# Create music directory
RUN mkdir /music

# Create a volume for the output directory
VOLUME /music

# Change CWD to /music
WORKDIR /music

RUN pwd

RUN ls -la

# Install requirements
#RUN poetry install

# Entrypoint command
#ENTRYPOINT ["poetry", "run", "spotdl"]
