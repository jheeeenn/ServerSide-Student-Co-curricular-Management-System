import cv2
from pyzbar import pyzbar
from map_graph import MapGraph

class QRNavigationSystem:
    def __init__(self):
        self.map = MapGraph()
        self.current_location = None
        self.destination = None

    def scan_qr(self, frame):
        qrcodes = pyzbar.decode(frame)
        for qr in qrcodes:
            data = qr.data.decode("utf-8")
            return data
        return None

    def get_user_destination(self):
        print("Select destination from:", list(self.map.graph.keys()))
        dest = input("Enter destination: ")
        while dest not in self.map.graph:
            dest = input("Invalid. Enter destination again: ")
        return dest

    def give_voice_instruction(self, instruction):
        print(f"Instruction: {instruction}")

    def run(self):
        cap = cv2.VideoCapture(0)
        print("Point camera at QR code to start")
        while True:
            ret, frame = cap.read()
            if not ret:
                continue
            qr_data = self.scan_qr(frame)
            if qr_data:
                self.current_location = qr_data
                self.destination = self.get_user_destination()
                route = self.map.get_route(self.current_location, self.destination)
                for i in range(len(route)-1):
                    instruction = self.map.get_instruction(route[i], route[i+1])
                    self.give_voice_instruction(instruction)
                print("Navigation complete")
                break
        cap.release()
        cv2.destroyAllWindows()
