# コンテナのベースとしてamazonlinux:2023を指定
FROM amazonlinux:2023

# nginxインストール
RUN dnf -y update
RUN dnf -y install nginx

EXPOSE 5173

ENTRYPOINT ["/usr/sbin/nginx", "-g", "daemon off;"]