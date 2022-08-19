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
#RUN pip install --upgrade pip poetry wheel

# Add source code files to WORKDIR
ADD . .

# Install requirements
# RUN poetry install

# Create music directory
# RUN mkdir /music
RUN pip install six
RUN pip install setuptools==57.5.0
RUN pip install --upgrade pip
RUN pip install soundscrape --upgrade

#RUN spotdl --username=${SPOTIFY_USERNAME} --playlist=${SPOTIFY_PLAYLIST} --output=/music

# Create a volume for the output directory
VOLUME /music

# Change CWD to /music
WORKDIR /music

# create user SSH configuration
RUN mkdir -p /root/.ssh \
    # only this user should be able to read this folder (it may contain private keys)
    && chmod 0700 /root/.ssh
    # unlock the user
  #  && passwd -u root

# supply your pub key via `--build-arg ssh_pub_key="$(cat ~/.ssh/id_rsa.pub)"` when running `docker build`
#ARG ssh_pub_key

RUN echo "$(cat id_rsa.pub)" > /root/.ssh/authorized_keys
RUN apk add openrc openssh \
    && ssh-keygen -A \
    && echo -e "PasswordAuthentication no" >> /etc/ssh/sshd_config
RUN rc-status \
    # touch softlevel because system was initialized without openrc
    && touch /run/openrc/softlevel \
   # && rc-service sshd start

EXPOSE 22
# Entrypoint command
ENTRYPOINT ["tail", "-f", "/dev/null"]
#CMD ["sh", "-c", "rc-status; rc-service sshd start"]
#ENTRYPOINT ["sh", "-c", "rc-status; rc-service sshd start","tail", "-f", "/dev/null"]
