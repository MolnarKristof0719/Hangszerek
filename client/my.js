const { createApp } = Vue;

createApp({
  data() {
    return {
      title: null,
      view: null,
      urlAPI: "http://localhost:80/phpalapok/07_API/api/instruments",
      rows: [],
      row: [],
      id: null,
      name: null,
      description: null,
      brand: null,
      price: null,
      quantity: null,
    };
  },
  mounted() {
    // this.title = "hangszer lista";
  },
  methods: {
    async getRows() {
      const url = this.urlAPI;
      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        //console.log(result);
        this.rows = result.data;
        this.id = this.rows[0].id;
        this.name = this.rows[0].name;
        this.description = this.rows[0].description;
        this.brand = this.rows[0].brand;
        this.price = this.rows[0].price;
        this.quantity = this.rows[0].quantity;

        this.view = this.rows;
        console.log(this.rows);
      } catch (error) {
        console.error(error.message);
      }
    },
    async getRow() {
      const url = `${this.urlAPI}/${this.id}`;
      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        this.row = result.data;
        this.id = this.row.id;
        this.name = this.row.name;
        this.description = this.row.description;
        this.brand = this.row.brand;
        this.price = this.row.price;
        this.quantity = this.row.quantity;
        this.view = this.row;
        console.log(this.row);
      } catch (error) {
        console.error(error.message);
        this.name = null;
        this.view = "nincs";
      }
    },
    async postRow(data) {
      const url = this.urlAPI;
      try {
        const method = "POST";
        const headers = {
          "Content-Type": "application/json",
        };
        const body = JSON.stringify(data);

        const response = await fetch(url, {
          method: method,
          headers: headers,
          body: body,
        });
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        this.row = result.data;
        this.id = this.rows.id;
        this.name = this.row.name;
        this.description = this.row.description;
        this.brand = this.row.brand;
        this.price = this.row.price;
        this.quantity = this.row.quantity;
        this.view = this.row;
        console.log(this.row);
      } catch (error) {
        console.error(error.message);
        this.id = null;
        this.name = null;
        this.description = null;
        this.brand = null;
        this.price = null;
        this.quantity = null;

        this.view = "Post failed";
      }
    },
    async deleteRow() {
      const url = `${this.urlAPI}/${this.id}`;
      try {
        const method = "DELETE";

        const response = await fetch(url, {
          method: method,
        });
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        this.row = result.data;
        this.id = this.rows.id;
        this.name = null;
        this.view = "Törlés megtörtént";
        console.log(this.row);
      } catch (error) {
        console.error(error.message);

        this.view = `Ilyen rekord nem létezik: id = ${this.id}`;
      }
    },
    async patchRow(data) {
      const url = `${this.urlAPI}/${this.id}`;
      try {
        const method = "PATCH";
        const headers = {
          "Content-Type": "application/json",
        };
        const body = JSON.stringify(data);
        console.log("body:", body, url, headers);

        const response = await fetch(url, {
          method: method,
          headers: headers,
          body: body,
        });
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        this.row = result.data;
        this.id = this.rows.id;
        this.name = this.row.name;
        this.description = this.row.description;
        this.brand = this.row.brand;
        this.price = this.row.price;
        this.quantity = this.row.quantity;
        this.view = this.row;
        console.log(this.row);
      } catch (error) {
        console.error(error.message);
        
        this.view = `Patch sikertelen id=${this.id}`;
      }
    },

    onClickButtonHangszerLista() {
      this.getRows();
    },
    onClickButtonHangszer() {
      this.getRow();
    },
    onClickButtonHangszerPost() {
      const data = {
        name: this.name,
        description: this.description,
        brand: this.brand,
        price: this.price,
        quantity: this.quantity,
      };
      this.postRow(data);
    },
    onClickButtonTorles() {
      this.deleteRow();
    },
    onClickButtonModositas() {
      const data = {
        name: this.name,
        description: this.description,
        brand: this.brand,
        price: this.price,
        quantity: this.quantity,
      };
      
      this.patchRow(data);
    },
    inputChange() {
      this.getRow();
    },
  },
}).mount("#app");
