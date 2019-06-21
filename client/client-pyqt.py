from PyQt5.QtCore import *
from PyQt5.QtWidgets import *
from PyQt5.QtGui import *
from PyQt5.QtWebEngineWidgets import *
import sys
import socket


class MainWindow(QMainWindow):
    # noinspection PyUnresolvedReferences
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # 设置窗口标题
        self.setWindowTitle('计网课程设计-李振威，郭崇山')

        # 设置窗口大小
        self.resize(1280, 720)

        # 设置浏览器
        self.browser = QWebEngineView()
        url = 'http://www.baidu.com/'
        # 指定打开界面的 URL
        self.browser.setUrl(QUrl(url))
        self.setCentralWidget(self.browser)

        # 添加导航栏
        navigation_bar = QToolBar('Navigation')
        # 设定图标的大小
        navigation_bar.setIconSize(QSize(200, 200))
        # 添加导航栏到窗口中
        self.addToolBar(navigation_bar)

        # 添加URL地址栏
        self.urlbar = QLineEdit()
        navigation_bar.addSeparator()  # 添加分隔符
        navigation_bar.addWidget(self.urlbar)
        self.urlbar.setText(url)
        self.urlbar.setText("jngcs.top/index.html")

        # 设置下拉框并添加到导航栏中
        self.chose_box = QComboBox()
        self.chose_box.addItem("TCP")
        self.chose_box.addItem("UDP")
        navigation_bar.addWidget(self.chose_box)

        # 设置按钮添加到导航栏
        self.button_send = QPushButton("发送请求")
        navigation_bar.addWidget(self.button_send)
        self.button_send.clicked.connect(self.button_action)

        # 添加信息显示框
        self.print_box = QTextBrowser(self)
        self.print_box.move(self.width() - 400, self.height() - 300)
        self.print_box.resize(400, 300)
        self.print_box.setReadOnly(True)


    def button_action(self):
        host = "47.100.43.103"  # 服务端IP
        port = 8080  # 设置端口
        url_connect = self.urlbar.displayText()  # 获取地址栏文本
        url_input=url_connect.split("/", 1)
        send_string = "GET http://"+url_connect+" HTTP/1,1\r\n" \
                      "Host: "+url_input[1]+"\r\n" \
                      "Connection: close\r\n" \
                      "User-Agent: Mozilla/5.0\r\n" \
                      "Accept-Language: cn\r\n"  # 请求报文

        if self.chose_box.currentText() == "TCP":
            print("TCP")
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)  # TCP套接字
            s.connect((host, port))
            # 发送数据:
            s.send(send_string.encode())
            data = s.recv(2048)
            # 关闭连接:
            s.close()
            header, html = data.split(b'\r\n\r\n', 1)
            self.browser.setHtml(html.decode())
            self.print_box.setText(data.decode())
            print(header.decode())
            print(html.decode())
        else:
            print("UDP")
            s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
            s.sendto(send_string.encode(), (host, port))
            data,addr = s.recvfrom(2048)
            s.close()
            header, html = data.split(b'\r\n\r\n', 1)
            self.browser.setHtml(html.decode())
            self.print_box.setText(data.decode())
            print(addr)
            print(header.decode())
            print(html.decode())


# 创建应用
app = QApplication(sys.argv)
# 创建主窗口
window = MainWindow()
# 显示窗口
window.show()
# 运行应用，并监听事件
app.exec_()