from websocket import create_connection

ws = create_connection("ws://192.168.8.1:7681/")
print("Start reading")
ws.send('{"GenRead":{"Antennas":"00000001","Q":1,"OpType":0,"LenTid":0,"PointerUserEvb":0,"LenUser":0}}')
ws.close()