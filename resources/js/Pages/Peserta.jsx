import React, { useEffect, useState } from "react";
import axios from "axios";

export default function Peserta() {
    const [data, setData] = useState([]);

    useEffect(() => {
        axios
            .get("/api/peserta")
            .then((response) => {
                setData(response.data);
            })
            .catch((error) => {
                console.error("There was an error fetching data!", error);
            });
    }, []);

    return (
        <div>
            <h1>Data Peserta</h1>
            <pre>{JSON.stringify(data, null, 2)}</pre>
        </div>
    );
}
