from websocket import create_connection

ws = create_connection("ws://192.168.8.1:7681/")
print("Stop RF")
ws.send('{"CloseRfPower":{}}')
ws.close()