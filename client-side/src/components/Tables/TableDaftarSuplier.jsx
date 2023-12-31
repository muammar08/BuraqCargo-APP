import React, { useState, useEffect }  from 'react'
import { Form, Container, Table, Row, Col } from 'react-bootstrap';
import '../../css/style.css';
import axios from 'axios';
import BASE_URL from '../../config';

function TableDaftarSuplier(){

    const [data, setData] = useState([]);
    const token = localStorage.getItem('token');
    const [search, setSearch] = useState('');
    const [searchResults, setSearchResults] = useState([]);
  
    const handleSearch = (e) => {
        setSearch(e.target.value);
     };


        useEffect (() => {
        if (!token) {
          window.location.href = '/';
        } else {
          axios.get(`${BASE_URL}/api/getsuplier`, {
            headers: {
              Authorization: `Bearer ${token}`,
              "ngrok-skip-browser-warning": "69420",
            },
          })
            .then((response) => {
                setData(response.data.data);
                }
            )
            .catch((error) => {
                console.error('Gagal mengambil data pengguna', error);
                }
            );
        }
        }, [token]);


        function searchTable() {
            const input = search.toLowerCase();
            const filteredData = data.filter((item) => {
                return (
                    item.nama_suplier.toLowerCase().includes(input)
                );
            });
            setSearchResults(filteredData);
        }

        useEffect(() => {
            searchTable();
        }, [search]);

        function capitalizeFirstLetter(str) {
            return str.toLowerCase().replace(/^(.)|\s+(.)/g, function ($1) {
              return $1.toUpperCase();
            });
          }

    return(

        <div>
            <Container className='p-4'>
                <Row>
                    <Col><h2>Daftar Suplier</h2></Col>
                    <Col className='text-end'>
                        <input
                        className="form-control"
                        type="search"
                        placeholder="Search"
                        aria-label="Search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        />
                    </Col>
                </Row>
                <Table responsive>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Suplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        {Array.isArray(searchResults) && searchResults.length > 0 ? (
                            searchResults.map((item, index) => (
                            <tr key={index}>
                                <td>{index + 1 }</td>
                                <td>{capitalizeFirstLetter(item.nama_suplier)}</td>
                            </tr>
                            ))
                        ) : (
                            Array.isArray(data) && data.length > 0 ? (
                            data.map((item, index) => (
                                <tr key={index}>
                                    <td>{index + 1 }</td>
                                    <td>{capitalizeFirstLetter(item.nama_suplier)}</td>
                                </tr>
                            ))
                            ) : (
                            <tr>
                                <td colSpan={3} style={{ textAlign: 'center' }}>Tidak ada data</td>
                            </tr>
                            )
                        )}
                    </tbody>
                </Table>
            </Container>
        </div>
    )
}

export default TableDaftarSuplier;

