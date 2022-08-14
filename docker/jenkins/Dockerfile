FROM jenkins/jenkins:latest

COPY apache-maven-3.8.6/ /opt/maven/

# eport maven to path
ENV PATH /opt/maven/bin:$PATH
# export maven home
ENV MAVEN_HOME /opt/maven:$MAVEN_HOME
# export maven version
